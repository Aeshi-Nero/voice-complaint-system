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

        // Create sample student
        User::updateOrCreate(
            ["id_number" => "STU001"],
            [
                "name" => "Maria Santos",
                "email" => "maria@student.com",
                "password" => Hash::make("password"),
                "role" => "student",
                "course" => "Computer Science",
                "is_blocked" => false,
                "complaints_today" => 0,
            ]
        );

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
