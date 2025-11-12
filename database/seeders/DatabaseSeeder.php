<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\AuthorizationSeeder;
use Database\Seeders\DefaultServicesSeeder;
use Database\Seeders\AdminUserSeeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Jalankan seeder inti aplikasi untuk production
        $this->call([
            AuthorizationSeeder::class,
            DefaultServicesSeeder::class,
            AdminUserSeeder::class,
        ]);

        // Opsional: contoh user dummy untuk development
        // Uncomment bila diperlukan di lokal
        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
