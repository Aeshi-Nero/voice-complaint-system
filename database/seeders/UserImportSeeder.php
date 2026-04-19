<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;

class UserImportSeeder extends Seeder
{
    public function run(): void
    {
        // Load the users from the extracted file
        require base_path('extracted_users.php');
        
        // $users is now available from the required file
        if (!isset($users) || !is_array($users)) {
            $this->command->error('Users array not found in extracted_users.php');
            return;
        }

        $importedUsers = [];

        foreach ($users as $userData) {
            // Generate random 4-digit password
            $rawPassword = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
            
            User::updateOrCreate(
                ['id_number' => $userData['id_number']],
                [
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                    'password' => Hash::make($rawPassword),
                    'role' => $userData['role'] ?? 'student',
                    'course' => $userData['course'] ?? null,
                    'is_blocked' => false,
                    'complaints_today' => 0,
                ]
            );

            // Store for reference file
            $userData['password'] = $rawPassword;
            $importedUsers[] = $userData;
        }

        // Save users with their plain-text passwords for the user to refer to
        $output = "<?php\n\nreturn " . var_export($importedUsers, true) . ";\n";
        File::put(base_path('imported_users_with_passwords.php'), $output);

        $this->command->info('Imported ' . count($users) . ' users.');
        $this->command->info('Passwords saved to imported_users_with_passwords.php');
        
        // Pick one for demonstration
        $example = $importedUsers[0];
        $this->command->info("Example login: ID: {$example['id_number']}, Password: {$example['password']}");
    }
}
