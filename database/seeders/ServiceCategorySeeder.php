<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ServiceCategory;

class ServiceCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaults = [
            ['name' => 'General', 'icon' => 'fa-broom', 'active' => true],
            ['name' => 'Karpet', 'icon' => 'fa-brush', 'active' => true],
            ['name' => 'Sofa', 'icon' => 'fa-couch', 'active' => true],
            ['name' => 'AC', 'icon' => 'fa-wind', 'active' => true],
            ['name' => 'Dapur', 'icon' => 'fa-utensils', 'active' => true],
            ['name' => 'Kamar Mandi', 'icon' => 'fa-shower', 'active' => true],
            ['name' => 'Lantai', 'icon' => 'fa-broom', 'active' => true],
        ];

        foreach ($defaults as $def) {
            $exists = ServiceCategory::where('name', $def['name'])->exists();
            if (! $exists) {
                ServiceCategory::create($def);
            }
        }
    }
}

