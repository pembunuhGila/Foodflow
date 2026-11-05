<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DemoRoleSwitcherTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Pastikan role switcher dapat beralih ke masing-masing 4 profil demo secara langsung.
     */
    public function test_can_switch_between_four_demo_profiles(): void
    {
        // Siapkan akun untuk 4 role jika belum ada di database pengujian
        $roles = [
            'admin' => UserRole::Admin,
            'gudang' => UserRole::Gudang,
            'produksi' => UserRole::Produksi,
            'owner' => UserRole::Owner,
        ];

        foreach ($roles as $key => $roleEnum) {
            User::firstOrCreate(
                ['email' => "{$key}@foodflow.test"],
                [
                    'name' => ucfirst($key).' Test User',
                    'password' => Hash::make('password'),
                    'role' => $roleEnum,
                    'status' => UserStatus::Aktif,
                ]
            );
        }

        // 1. Tes beralih ke Admin
        $response = $this->get(route('demo.switch-role', ['role' => 'admin']));
        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticated();
        $this->assertEquals(UserRole::Admin, auth()->user()->role);

        // 2. Tes beralih ke Gudang tanpa logout manual
        $response = $this->get(route('demo.switch-role', ['role' => 'gudang']));
        $response->assertRedirect(route('gudang.dashboard'));
        $this->assertAuthenticated();
        $this->assertEquals(UserRole::Gudang, auth()->user()->role);

        // 3. Tes beralih ke Produksi
        $response = $this->get(route('demo.switch-role', ['role' => 'produksi']));
        $response->assertRedirect(route('produksi.dashboard'));
        $this->assertAuthenticated();
        $this->assertEquals(UserRole::Produksi, auth()->user()->role);

        // 4. Tes beralih ke Owner
        $response = $this->get(route('demo.switch-role', ['role' => 'owner']));
        $response->assertRedirect(route('owner.dashboard'));
        $this->assertAuthenticated();
        $this->assertEquals(UserRole::Owner, auth()->user()->role);
    }

    /**
     * Pastikan request dengan role invalid ditolak dengan error.
     */
    public function test_invalid_role_switch_is_rejected(): void
    {
        $response = $this->get(route('demo.switch-role', ['role' => 'hacker_role']));
        $response->assertSessionHasErrors('demo');
    }
}
