<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;
use Illuminate\Support\Str;

class DefaultServicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure at least 3 distinct categories exist: General, Karpet, Sofa
        $defaults = [
            [
                'name' => 'General Cleaning',
                'category' => 'General',
                'icon' => 'fa-broom',
                'base_price' => 150000,
                'duration_minutes' => 60,
                'description' => 'Pembersihan umum untuk rumah/apartemen.',
            ],
            [
                'name' => 'Karpet Cleaning',
                'category' => 'Karpet',
                'icon' => 'fa-rug',
                'base_price' => 120000,
                'duration_minutes' => 45,
                'description' => 'Pembersihan dan perawatan karpet.',
            ],
            [
                'name' => 'Sofa Cleaning',
                'category' => 'Sofa',
                'icon' => 'fa-couch',
                'base_price' => 100000,
                'duration_minutes' => 60,
                'description' => 'Pembersihan sofa dan upholstery.',
            ],
        ];

        foreach ($defaults as $def) {
            $exists = Service::where('category', $def['category'])->exists();
            if (! $exists) {
                Service::create([
                    'name' => $def['name'],
                    'description' => $def['description'],
                    'base_price' => $def['base_price'],
                    'duration_minutes' => $def['duration_minutes'],
                    'category' => $def['category'],
                    'icon' => $def['icon'],
                    'active' => true,
                    'slug' => Str::slug($def['name']),
                ]);
            }
        }
    }
}

