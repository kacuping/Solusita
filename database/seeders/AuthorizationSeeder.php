<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class AuthorizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat roles dasar
        $admin = Role::firstOrCreate(['slug' => 'administrator'], [
            'name' => 'Administrator',
            'description' => 'Memiliki akses penuh, dapat mengelola role & izin',
        ]);

        $staff = Role::firstOrCreate(['slug' => 'staff'], [
            'name' => 'Staff',
            'description' => 'Akses terbatas sesuai izin yang diberikan admin',
        ]);

        // Daftar permissions umum (bisa ditambah sesuai kebutuhan)
        $keys = [
            // Dashboard & umum
            ['key' => 'dashboard.view', 'name' => 'Lihat Dashboard'],

            // Users
            ['key' => 'users.view', 'name' => 'Lihat User'],
            ['key' => 'users.manage', 'name' => 'Kelola User'],

            // Roles & Izin
            ['key' => 'roles.manage', 'name' => 'Kelola Role & Izin'],

            // Approval (persetujuan verifikasi)
            ['key' => 'approvals.view', 'name' => 'Lihat Halaman Approval'],
            ['key' => 'approvals.manage', 'name' => 'Kelola Approval'],

            // Contoh modul lain (sesuaikan dengan menu Anda)
            ['key' => 'bookings.view', 'name' => 'Lihat Pesanan'],
            ['key' => 'bookings.manage', 'name' => 'Kelola Pesanan'],
            ['key' => 'services.view', 'name' => 'Lihat Layanan'],
            ['key' => 'services.manage', 'name' => 'Kelola Layanan'],
            ['key' => 'customers.view', 'name' => 'Lihat Pelanggan'],
            ['key' => 'customers.manage', 'name' => 'Kelola Pelanggan'],

            // Petugas (Cleaners)
            ['key' => 'cleaners.view', 'name' => 'Lihat Petugas'],
            ['key' => 'cleaners.manage', 'name' => 'Kelola Petugas'],
        ];

        $permissions = collect($keys)->map(function ($data) {
            return Permission::firstOrCreate(['key' => $data['key']], [
                'name' => $data['name'],
            ]);
        });

        // Beri semua izin ke Administrator secara default
        $admin->permissions()->syncWithoutDetaching($permissions->pluck('id'));

        // Staff default: hanya view pada beberapa modul dasar
        $staffDefault = $permissions->whereIn('key', [
            'dashboard.view',
            'bookings.view',
            'services.view',
            'customers.view',
            'users.view',
            'cleaners.view',
        ])->pluck('id');

        $staff->permissions()->syncWithoutDetaching($staffDefault);
    }
}
