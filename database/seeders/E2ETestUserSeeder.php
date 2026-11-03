<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class E2ETestUserSeeder extends Seeder
{
    /**
     * Akun demo khusus testing otomatis E2E dan demonstrasi aplikasi.
     * Menggunakan password seragam 'password123' yang mudah diingat dan aman.
     */
    public function run(): void
    {
        $demoAccounts = [
            [
                'name' => 'Demo Admin FoodFlow',
                'email' => 'demo.admin@foodflow.test',
                'password' => Hash::make('password123'),
                'role' => UserRole::Admin,
                'status' => UserStatus::Aktif,
            ],
            [
                'name' => 'Demo Staff Gudang',
                'email' => 'demo.gudang@foodflow.test',
                'password' => Hash::make('password123'),
                'role' => UserRole::Gudang,
                'status' => UserStatus::Aktif,
            ],
            [
                'name' => 'Demo Staff Produksi',
                'email' => 'demo.produksi@foodflow.test',
                'password' => Hash::make('password123'),
                'role' => UserRole::Produksi,
                'status' => UserStatus::Aktif,
            ],
            [
                'name' => 'Demo Owner Manager',
                'email' => 'demo.owner@foodflow.test',
                'password' => Hash::make('password123'),
                'role' => UserRole::Owner,
                'status' => UserStatus::Aktif,
            ],
            [
                'name' => 'Demo Akun Nonaktif',
                'email' => 'demo.inactive@foodflow.test',
                'password' => Hash::make('password123'),
                'role' => UserRole::Gudang,
                'status' => UserStatus::TidakAktif,
            ],
        ];

        foreach ($demoAccounts as $account) {
            User::updateOrCreate(
                ['email' => $account['email']],
                $account
            );
        }
    }
}
