import sqlite3
import random
import re
import os

# Database path
db_path = 'database/database.sqlite'
extracted_file = 'extracted_users.php'
output_file = 'imported_users_with_passwords.php'

def parse_php_users(file_path):
    with open(file_path, 'r') as f:
        content = f.read()
    
    user_blocks = re.findall(r"\[(.*?)\]", content, re.DOTALL)
    users = []
    for block in user_blocks:
        user = {}
        for key in ['name', 'email', 'id_number', 'course', 'role']:
            match = re.search(f"'{key}' => '(.*?)'", block)
            if match:
                user[key] = match.group(1)
        if user:
            users.append(user)
    return users

def fix_schema(conn):
    cursor = conn.cursor()
    print("Recreating users table with correct schema...")
    
    # Check if we need to backup old data
    cursor.execute("SELECT name FROM sqlite_master WHERE type='table' AND name='users'")
    if cursor.fetchone():
        cursor.execute("ALTER TABLE users RENAME TO users_old")
    
    # Create new table with correct schema
    cursor.execute("""
    CREATE TABLE users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        id_number VARCHAR UNIQUE,
        name VARCHAR NOT NULL,
        email VARCHAR UNIQUE NOT NULL,
        password VARCHAR NOT NULL,
        role VARCHAR DEFAULT 'student',
        is_blocked BOOLEAN DEFAULT 0,
        complaints_today INTEGER DEFAULT 0,
        last_complaint_reset DATETIME,
        course VARCHAR,
        created_at DATETIME,
        updated_at DATETIME
    )
    """)
    
    # Try to migrate old users if any (Admin etc)
    cursor.execute("SELECT name FROM sqlite_master WHERE type='table' AND name='users_old'")
    if cursor.fetchone():
        try:
            # Basic migration for common fields
            cursor.execute("INSERT INTO users (name, email, password, created_at, updated_at) SELECT name, email, password, created_at, updated_at FROM users_old")
            # Set a dummy id_number for old users to satisfy unique constraint
            cursor.execute("UPDATE users SET id_number = 'OLD_' || id WHERE id_number IS NULL")
        except Exception as e:
            print(f"Migration warning: {e}")
        finally:
            cursor.execute("DROP TABLE users_old")

def import_users(users, conn):
    cursor = conn.cursor()
    imported_list = []
    
    for user in users:
        raw_password = str(random.randint(0, 9999)).zfill(4)
        
        try:
            cursor.execute("""
                INSERT OR REPLACE INTO users (id_number, name, email, password, role, course, is_blocked, complaints_today)
                VALUES (?, ?, ?, ?, ?, ?, 0, 0)
            """, (user['id_number'], user['name'], user['email'], raw_password, user.get('role', 'student'), user.get('course')))
            
            user['password'] = raw_password
            imported_list.append(user)
        except Exception as e:
            print(f"Error importing {user['id_number']}: {e}")
            
    conn.commit()
    return imported_list

def write_reference_file(users, output_path):
    with open(output_path, 'w') as f:
        f.write("<?php\n\nreturn [\n")
        for u in users:
            f.write(f"    ['id_number' => '{u['id_number']}', 'name' => \"{u['name']}\", 'password' => '{u['password']}'],\n")
        f.write("];\n")

if __name__ == "__main__":
    if not os.path.exists(db_path):
        # Create empty db if not exists
        os.makedirs(os.path.dirname(db_path), exist_ok=True)
        open(db_path, 'w').close()
        
    users = parse_php_users(extracted_file)
    print(f"Parsed {len(users)} users from PHP file.")
    
    conn = sqlite3.connect(db_path)
    fix_schema(conn)
    imported = import_users(users, conn)
    conn.close()
    
    write_reference_file(imported, output_file)
    print(f"Successfully imported {len(imported)} users to SQLite.")
    print(f"Reference file created at {output_file}")
    if imported:
        print(f"Example login: ID {imported[0]['id_number']}, Pass {imported[0]['password']}")
