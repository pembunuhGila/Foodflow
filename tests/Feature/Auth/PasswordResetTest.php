<?php

namespace Tests\Feature\Auth;

use App\Enums\PasswordResetStatus;
use App\Models\PasswordResetRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_forgot_password_screen_can_be_rendered(): void
    {
        $response = $this->get('/forgot-password');

        $response->assertStatus(200);
        $response->assertSee('Lupa kata sandi');
    }

    public function test_user_can_submit_password_reset_request(): void
    {
        $user = User::factory()->create(['email' => 'user@foodflow.test']);

        $response = $this->post('/forgot-password', [
            'email' => 'user@foodflow.test',
        ]);

        $response->assertSessionHas('status');
        $this->assertDatabaseHas('password_reset_requests', [
            'user_id' => $user->id,
            'email' => 'user@foodflow.test',
            'status' => PasswordResetStatus::Diajukan->value,
        ]);
    }

    public function test_user_cannot_submit_reset_request_with_unregistered_email(): void
    {
        $response = $this->post('/forgot-password', [
            'email' => 'tidakada@foodflow.test',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertDatabaseMissing('password_reset_requests', [
            'email' => 'tidakada@foodflow.test',
        ]);
    }

    public function test_admin_can_view_password_reset_requests_list(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create(['name' => 'Budi Pemohon']);
        PasswordResetRequest::create([
            'user_id' => $user->id,
            'email' => $user->email,
            'status' => PasswordResetStatus::Diajukan,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.password-resets.index'));

        $response->assertStatus(200);
        $response->assertSee('Permintaan Pemulihan Kata Sandi');
        $response->assertSee('Budi Pemohon');
    }

    public function test_non_admin_cannot_access_password_reset_requests(): void
    {
        $gudang = User::factory()->gudang()->create();

        $this->actingAs($gudang)->get(route('admin.password-resets.index'))->assertStatus(403);
    }

    public function test_admin_can_approve_password_reset_request_and_update_password(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create([
            'email' => 'staff@foodflow.test',
            'password' => Hash::make('old-password'),
        ]);

        $resetRequest = PasswordResetRequest::create([
            'user_id' => $user->id,
            'email' => $user->email,
            'status' => PasswordResetStatus::Diajukan,
        ]);

        $response = $this->actingAs($admin)->put(route('admin.password-resets.approve', $resetRequest), [
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
            'notes' => 'Password baru telah dikirimkan ke pemohon.',
        ]);

        $response->assertRedirect(route('admin.password-resets.index'));
        $response->assertSessionHas('status');

        $resetRequest->refresh();
        $user->refresh();

        $this->assertSame(PasswordResetStatus::Selesai, $resetRequest->status);
        $this->assertSame($admin->id, $resetRequest->processed_by);
        $this->assertNotNull($resetRequest->processed_at);

        // Verifikasi bahwa user kini dapat login dengan password baru
        $this->assertTrue(Hash::check('new-password-123', $user->password));

        // Logout admin agar session bersih sebelum uji coba login user
        $this->post('/logout');

        $loginResponse = $this->post('/login', [
            'email' => 'staff@foodflow.test',
            'password' => 'new-password-123',
        ]);

        $loginResponse->assertRedirect(route($user->getDashboardRoute(), absolute: false));
    }

    public function test_admin_can_reject_password_reset_request(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();

        $resetRequest = PasswordResetRequest::create([
            'user_id' => $user->id,
            'email' => $user->email,
            'status' => PasswordResetStatus::Diajukan,
        ]);

        $response = $this->actingAs($admin)->patch(route('admin.password-resets.reject', $resetRequest), [
            'notes' => 'Permintaan tidak valid.',
        ]);

        $response->assertRedirect(route('admin.password-resets.index'));

        $resetRequest->refresh();
        $this->assertSame(PasswordResetStatus::Gagal, $resetRequest->status);
        $this->assertSame('Permintaan tidak valid.', $resetRequest->admin_notes);
    }
}
