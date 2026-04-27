<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\ProfanityWord;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create superadmin user
        User::updateOrCreate(
            ["id_number" => "SADMIN001"],
            [
                "name" => "Dr. Julian Thorne",
                "email" => "superadmin@voice.com",
                "password" => Hash::make("password"),
                "role" => "superadmin",
                "course" => "Provost Office",
                "is_blocked" => false,
                "complaints_today" => 0,
            ]
        );

        // Create admin user
        User::updateOrCreate(
            ["id_number" => "ADMIN001"],
            [
                "name" => "System Administrator",
                "email" => "admin@voice.com",
                "password" => Hash::make("password"),
                "role" => "admin",
                "course" => "Administration",
                "is_blocked" => false,
                "complaints_today" => 0,
            ]
        );

        // Create second sample student
        User::updateOrCreate(
            ["id_number" => "STU002"],
            [
                "name" => "Juan Dela Cruz",
                "email" => "juan@student.com",
                "password" => Hash::make("password"),
                "role" => "student",
                "course" => "Information Technology",
                "is_blocked" => false,
                "complaints_today" => 0,
                "is_first_login" => false, // Set to false so user can log in immediately
            ]
        );

        // Ensure STU001 is also accessible for normal login
        User::where('id_number', 'STU001')->update(['is_first_login' => false]);

        // Sample profanity words
        $profanityWords = [
            "fuck", "shit", "bitch", "damn", "hell", "asshole", "bastard",
            "crap", "dick", "piss", "cunt", "whore", "slut", "nigger",
            "faggot", "retard", "stupid", "idiot", "moron", "dumbass"
        ];

        foreach ($profanityWords as $word) {
            ProfanityWord::updateOrCreate(
                ["word" => $word],
                ["word" => $word]
            );
        }
    }
}
