<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database with sample accounts for all 4 roles.
     */
    public function run(): void
    {
        $accounts = [
            [
                'name' => 'Admin FoodFlow',
                'email' => 'admin@foodflow.test',
                'password' => Hash::make('password'),
                'role' => UserRole::Admin,
                'status' => UserStatus::Aktif,
            ],
            [
                'name' => 'Staff Gudang',
                'email' => 'gudang@foodflow.test',
                'password' => Hash::make('password'),
                'role' => UserRole::Gudang,
                'status' => UserStatus::Aktif,
            ],
            [
                'name' => 'Staff Produksi',
                'email' => 'produksi@foodflow.test',
                'password' => Hash::make('password'),
                'role' => UserRole::Produksi,
                'status' => UserStatus::Aktif,
            ],
            [
                'name' => 'Owner Manager',
                'email' => 'owner@foodflow.test',
                'password' => Hash::make('password'),
                'role' => UserRole::Owner,
                'status' => UserStatus::Aktif,
            ],
        ];

        foreach ($accounts as $account) {
            User::updateOrCreate(
                ['email' => $account['email']],
                $account
            );
        }
    }
}
