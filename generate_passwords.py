import random
import re

extracted_file = 'extracted_users.php'
output_file = 'imported_users_with_passwords.php'

def parse_php_users(file_path):
    try:
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
    except Exception as e:
        print(f"Error parsing: {e}")
        return []

def write_reference_file(users, output_path):
    with open(output_path, 'w') as f:
        f.write("<?php\n\nreturn [\n")
        for u in users:
            # Generate random 4-digit password here
            raw_password = str(random.randint(0, 9999)).zfill(4)
            f.write(f"    ['id_number' => '{u['id_number']}', 'name' => \"{u['name']}\", 'password' => '{raw_password}'],\n")
        f.write("];\n")

if __name__ == "__main__":
    users = parse_php_users(extracted_file)
    if users:
        print(f"Parsed {len(users)} users. Generating passwords and reference file...")
        write_reference_file(users, output_file)
        print(f"Successfully created {output_file}")
    else:
        print("No users found to process.")
