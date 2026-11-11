<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SidebarNavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_sidebar_has_dropdown_accordion_controls(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);

        $response = $this->actingAs($admin)->get(route($admin->getDashboardRoute()));

        $response->assertOk();
        // Role Admin memiliki 13 menu (>= 6) sehingga grup non-utama memiliki button dropdown toggle
        $response->assertSee('data-sidebar-toggle="true"', false);
        $response->assertSee('Master Data');
        $response->assertSee('Pengadaan');
    }

    public function test_produksi_sidebar_has_no_dropdown_when_items_less_than_six(): void
    {
        $produksi = User::factory()->create(['role' => UserRole::Produksi]);

        $response = $this->actingAs($produksi)->get(route($produksi->getDashboardRoute()));

        $response->assertOk();
        // Role Produksi memiliki 5 menu (< 6) sehingga TIDAK ada accordion dropdown button
        $response->assertDontSee('data-sidebar-toggle="true"', false);
        $response->assertSee('Dapur');
        $response->assertSee('Rencana Produksi');
        $response->assertSee('Realisasi Masak');
        $response->assertSee('Menu & Nilai Gizi');
    }

    public function test_owner_sidebar_has_no_dropdown_when_items_less_than_six(): void
    {
        $owner = User::factory()->create(['role' => UserRole::Owner]);

        $response = $this->actingAs($owner)->get(route($owner->getDashboardRoute()));

        $response->assertOk();
        // Role Owner memiliki 5 menu (< 6) sehingga TIDAK ada accordion dropdown button
        $response->assertDontSee('data-sidebar-toggle="true"', false);
        $response->assertSee('Pemantauan');
        $response->assertSee('Monitoring Produksi');
        $response->assertSee('Cetak Laporan');
    }

    public function test_gudang_sidebar_has_dropdown_controls_when_items_are_six(): void
    {
        $gudang = User::factory()->create(['role' => UserRole::Gudang]);

        $response = $this->actingAs($gudang)->get(route($gudang->getDashboardRoute()));

        $response->assertOk();
        // Role Gudang memiliki 6 menu (>= 6)
        $response->assertSee('data-sidebar-toggle="true"', false);
        $response->assertSee('Persediaan');
        $response->assertSee('Pengadaan');
    }
}
