<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FlashAlertColorTest extends TestCase
{
    use RefreshDatabase;

    private User $produksiUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->produksiUser = User::factory()->create([
            'role' => UserRole::Produksi,
        ]);
    }

    public function test_success_alert_renders_green_theme_when_creating_or_updating(): void
    {
        $response = $this->actingAs($this->produksiUser)
            ->withSession(['success' => 'Rencana produksi PRD-20261111-0001 berhasil disimpan.'])
            ->get(route('production-plans.index'));

        $response->assertOk();
        $response->assertSee('bg-success-bg', false);
        $response->assertSee('text-success-text', false);
        $response->assertSee('Rencana produksi PRD-20261111-0001 berhasil disimpan.');
    }

    public function test_delete_alert_renders_red_theme_with_trash_icon_when_deleted(): void
    {
        $response = $this->actingAs($this->produksiUser)
            ->withSession(['success' => 'Rencana produksi PRD-20261111-0001 berhasil dihapus.'])
            ->get(route('production-plans.index'));

        $response->assertOk();
        $response->assertSee('bg-danger-bg', false);
        $response->assertSee('text-danger-text', false);
        $response->assertSee('Rencana produksi PRD-20261111-0001 berhasil dihapus.');
    }

    public function test_error_alert_renders_red_danger_theme(): void
    {
        $response = $this->actingAs($this->produksiUser)
            ->withSession(['error' => 'Terjadi kesalahan sistem saat memproses produksi.'])
            ->get(route('production-plans.index'));

        $response->assertOk();
        $response->assertSee('bg-danger-bg', false);
        $response->assertSee('text-danger-text', false);
        $response->assertSee('Terjadi kesalahan sistem saat memproses produksi.');
    }

    public function test_warning_alert_renders_yellow_theme(): void
    {
        $response = $this->actingAs($this->produksiUser)
            ->withSession(['warning' => 'Rencana produksi sudah memiliki catatan operasional.'])
            ->get(route('production-plans.index'));

        $response->assertOk();
        $response->assertSee('bg-warning-bg', false);
        $response->assertSee('text-warning-text', false);
        $response->assertSee('Rencana produksi sudah memiliki catatan operasional.');
    }
}
