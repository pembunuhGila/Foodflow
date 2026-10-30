<?php

namespace Tests\Feature\Admin;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_users_list(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->gudang()->create(['name' => 'Staf Gudang 1']);

        $response = $this->actingAs($admin)->get(route('admin.users.index'));

        $response->assertStatus(200);
        $response->assertSee('Kelola Data Pengguna');
        $response->assertSee('Staf Gudang 1');
    }

    public function test_non_admin_cannot_access_user_management(): void
    {
        $gudang = User::factory()->gudang()->create();
        $produksi = User::factory()->produksi()->create();
        $owner = User::factory()->owner()->create();

        $this->actingAs($gudang)->get(route('admin.users.index'))->assertStatus(403);
        $this->actingAs($produksi)->get(route('admin.users.index'))->assertStatus(403);
        $this->actingAs($owner)->get(route('admin.users.index'))->assertStatus(403);
    }

    public function test_guest_is_redirected_to_login_when_accessing_user_management(): void
    {
        $response = $this->get(route('admin.users.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_admin_can_filter_users_by_search_and_role(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->gudang()->create(['name' => 'Budi Gudang', 'email' => 'budi@test.com']);
        User::factory()->produksi()->create(['name' => 'Siti Produksi', 'email' => 'siti@test.com']);

        $response = $this->actingAs($admin)->get(route('admin.users.index', [
            'search' => 'Budi',
            'role' => UserRole::Gudang->value,
        ]));

        $response->assertStatus(200);
        $response->assertSee('Budi Gudang');
        $response->assertDontSee('Siti Produksi');
    }

    public function test_admin_can_view_create_user_form(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('admin.users.create'));

        $response->assertStatus(200);
        $response->assertSee('Pendaftaran Akun Pengguna Baru');
    }

    public function test_admin_can_register_new_user_with_role(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Ahmad Kasir',
            'email' => 'ahmad@foodflow.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => UserRole::Produksi->value,
            'status' => UserStatus::Aktif->value,
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('status');

        $this->assertDatabaseHas('users', [
            'email' => 'ahmad@foodflow.test',
            'role' => UserRole::Produksi->value,
            'status' => UserStatus::Aktif->value,
        ]);
    }

    public function test_admin_cannot_register_user_with_duplicate_email(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->create(['email' => 'duplikat@foodflow.test']);

        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'User Lain',
            'email' => 'duplikat@foodflow.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => UserRole::Gudang->value,
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_admin_can_view_edit_user_form(): void
    {
        $admin = User::factory()->admin()->create();
        $targetUser = User::factory()->gudang()->create();

        $response = $this->actingAs($admin)->get(route('admin.users.edit', $targetUser));

        $response->assertStatus(200);
        $response->assertSee('Ubah Data Pengguna');
        $response->assertSee($targetUser->email);
    }

    public function test_admin_can_update_user_information_and_role(): void
    {
        $admin = User::factory()->admin()->create();
        $targetUser = User::factory()->gudang()->create();

        $response = $this->actingAs($admin)->put(route('admin.users.update', $targetUser), [
            'name' => 'Nama Baru',
            'email' => 'namabaru@foodflow.test',
            'role' => UserRole::Owner->value,
            'status' => UserStatus::Aktif->value,
        ]);

        $response->assertRedirect(route('admin.users.index'));

        $targetUser->refresh();
        $this->assertSame('Nama Baru', $targetUser->name);
        $this->assertSame('namabaru@foodflow.test', $targetUser->email);
        $this->assertSame(UserRole::Owner, $targetUser->role);
    }

    public function test_admin_can_toggle_user_status_between_active_and_inactive(): void
    {
        $admin = User::factory()->admin()->create();
        $targetUser = User::factory()->create(['status' => UserStatus::Aktif]);

        // Nonaktifkan
        $response = $this->actingAs($admin)->patch(route('admin.users.toggle-status', $targetUser));
        $response->assertRedirect(route('admin.users.index'));
        $targetUser->refresh();
        $this->assertSame(UserStatus::TidakAktif, $targetUser->status);

        // Aktifkan kembali
        $this->actingAs($admin)->patch(route('admin.users.toggle-status', $targetUser));
        $targetUser->refresh();
        $this->assertSame(UserStatus::Aktif, $targetUser->status);
    }

    public function test_admin_cannot_deactivate_own_account(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->patch(route('admin.users.toggle-status', $admin));

        $response->assertSessionHasErrors('status');
        $admin->refresh();
        $this->assertSame(UserStatus::Aktif, $admin->status);
    }

    public function test_admin_can_reset_user_password(): void
    {
        $admin = User::factory()->admin()->create();
        $targetUser = User::factory()->create(['password' => Hash::make('old-password')]);

        $response = $this->actingAs($admin)->put(route('admin.users.reset-password', $targetUser), [
            'password' => 'new-secure-password123',
            'password_confirmation' => 'new-secure-password123',
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $targetUser->refresh();

        $this->assertTrue(Hash::check('new-secure-password123', $targetUser->password));
    }
}
