<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'uid' => 'ADMIN001',
            'name' => 'Admin User',
            'email' => 'admin@kku.ac.th',
            'email_verified_at' => now(),
            'phonenumber' => '0812345678',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'ip_address' => '127.0.0.1',
            'is_banned' => false,
            'ban_reason' => null,
            'banned_at' => null,
            'banned_by' => null,
        ]);

        $this->command->info('Admin user created successfully!');
        $this->command->info('Email: admin@kku.ac.th');
        $this->command->info('Password: admin123');
    }
}
