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
        $email = env('ADMIN_EMAIL', 'admin@solusita.com');
        $password = env('ADMIN_PASSWORD', 'admin12345');
        $name = env('ADMIN_NAME', 'Administrator');

        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make($password),
                'role' => 'administrator',
                'email_verified_at' => now(),
            ]
        );
    }
}
