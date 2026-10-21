<?php

namespace Tests\Feature;

use App\Enums\ProductionPlanStatus;
use App\Enums\UserRole;
use App\Models\Product;
use App\Models\ProductionPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductionPlanListTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $produksi;

    private User $gudang;

    private User $owner;

    private Product $productA;

    private Product $productB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => UserRole::Admin]);
        $this->produksi = User::factory()->create(['role' => UserRole::Produksi]);
        $this->gudang = User::factory()->create(['role' => UserRole::Gudang]);
        $this->owner = User::factory()->create(['role' => UserRole::Owner]);

        $this->productA = Product::factory()->create([
            'name' => 'Ayam Panggang Klaten Sehat',
            'unit' => 'porsi',
            'selling_price' => 28000,
            'is_active' => true,
        ]);

        $this->productB = Product::factory()->create([
            'name' => 'Tumis Buncis Jagung Manis',
            'unit' => 'porsi',
            'selling_price' => 15000,
            'is_active' => true,
        ]);
    }

    /**
     * 1. Pengguna role 'produksi' yang sudah login dapat mengakses halaman.
     */
    public function test_produksi_user_can_access_production_plans_list(): void
    {
        $response = $this->actingAs($this->produksi)->get(route('production-plans.index'));

        $response->assertOk();
        $response->assertSee('Daftar Rencana Produksi Dapur');
    }

    /**
     * 2. Pengguna role 'admin' yang sudah login dapat mengakses halaman.
     */
    public function test_admin_user_can_access_production_plans_list(): void
    {
        $response = $this->actingAs($this->admin)->get(route('production-plans.index'));

        $response->assertOk();
        $response->assertSee('Daftar Rencana Produksi Dapur');
    }

    /**
     * 3. Pengguna yang belum login tidak dapat mengakses halaman (dialihkan ke login).
     */
    public function test_unauthenticated_user_cannot_access_production_plans_list(): void
    {
        $response = $this->get(route('production-plans.index'));

        $response->assertRedirect(route('login'));
    }

    /**
     * 4. Pengguna dengan role lain (selain admin dan produksi) ditolak dengan 403 Forbidden.
     */
    public function test_other_roles_are_forbidden_from_accessing_production_plans_list(): void
    {
        $gudangResponse = $this->actingAs($this->gudang)->get(route('production-plans.index'));
        $gudangResponse->assertForbidden();

        $ownerResponse = $this->actingAs($this->owner)->get(route('production-plans.index'));
        $ownerResponse->assertForbidden();
    }

    /**
     * 5. Daftar menampilkan data rencana produksi beserta informasi menu yang relevan.
     */
    public function test_list_displays_production_plans_data_with_relevant_menu_information(): void
    {
        $plan = ProductionPlan::create([
            'plan_number' => 'PLN-20261021-0001',
            'plan_date' => '2026-10-20',
            'production_date' => '2026-10-21',
            'status' => ProductionPlanStatus::Scheduled,
            'created_by' => $this->produksi->id,
            'notes' => 'Menu makan siang katering sekolah',
        ]);

        $plan->details()->create([
            'product_id' => $this->productA->id,
            'planned_quantity' => 120,
        ]);

        $plan->details()->create([
            'product_id' => $this->productB->id,
            'planned_quantity' => 80,
        ]);

        $response = $this->actingAs($this->produksi)->get(route('production-plans.index'));

        $response->assertOk();
        $response->assertSee('PLN-20261021-0001');
        $response->assertSee('21/10/2026');
        $response->assertSee('200 Porsi');
        $response->assertSee('Ayam Panggang Klaten Sehat');
        $response->assertSee('Tumis Buncis Jagung Manis');
        $response->assertSee($this->produksi->name);
        $response->assertSee('Dijadwalkan');
        $response->assertSee('Scheduled');
    }

    /**
     * 6. Filter tanggal bekerja dengan benar secara mandiri.
     */
    public function test_filter_by_production_date_works_correctly(): void
    {
        $planDate1 = ProductionPlan::create([
            'plan_number' => 'PLN-DATE1-0001',
            'plan_date' => '2026-10-20',
            'production_date' => '2026-10-25',
            'status' => ProductionPlanStatus::Scheduled,
            'created_by' => $this->produksi->id,
        ]);
        $planDate1->details()->create([
            'product_id' => $this->productA->id,
            'planned_quantity' => 50,
        ]);

        $planDate2 = ProductionPlan::create([
            'plan_number' => 'PLN-DATE2-0002',
            'plan_date' => '2026-10-20',
            'production_date' => '2026-10-28',
            'status' => ProductionPlanStatus::Scheduled,
            'created_by' => $this->produksi->id,
        ]);
        $planDate2->details()->create([
            'product_id' => $this->productB->id,
            'planned_quantity' => 60,
        ]);

        $response = $this->actingAs($this->produksi)->get(route('production-plans.index', [
            'production_date' => '2026-10-25',
        ]));

        $response->assertOk();
        $response->assertSee('PLN-DATE1-0001');
        $response->assertDontSee('PLN-DATE2-0002');
    }

    /**
     * 7. Filter status bekerja dengan benar secara mandiri (Scheduled, In Progress, Completed).
     */
    public function test_filter_by_status_works_correctly(): void
    {
        $planScheduled = ProductionPlan::create([
            'plan_number' => 'PLN-STATUS-SCHED',
            'plan_date' => '2026-10-20',
            'production_date' => '2026-10-22',
            'status' => ProductionPlanStatus::Scheduled,
            'created_by' => $this->produksi->id,
        ]);
        $planScheduled->details()->create([
            'product_id' => $this->productA->id,
            'planned_quantity' => 10,
        ]);

        $planInProgress = ProductionPlan::create([
            'plan_number' => 'PLN-STATUS-INPROG',
            'plan_date' => '2026-10-20',
            'production_date' => '2026-10-22',
            'status' => ProductionPlanStatus::InProgress,
            'created_by' => $this->produksi->id,
        ]);
        $planInProgress->details()->create([
            'product_id' => $this->productA->id,
            'planned_quantity' => 20,
        ]);

        $planCompleted = ProductionPlan::create([
            'plan_number' => 'PLN-STATUS-COMPL',
            'plan_date' => '2026-10-20',
            'production_date' => '2026-10-22',
            'status' => ProductionPlanStatus::Completed,
            'created_by' => $this->admin->id,
        ]);
        $planCompleted->details()->create([
            'product_id' => $this->productB->id,
            'planned_quantity' => 30,
        ]);

        // Filter: Scheduled
        $resSched = $this->actingAs($this->produksi)->get(route('production-plans.index', ['status' => 'scheduled']));
        $resSched->assertOk();
        $resSched->assertSee('PLN-STATUS-SCHED');
        $resSched->assertDontSee('PLN-STATUS-INPROG');
        $resSched->assertDontSee('PLN-STATUS-COMPL');

        // Filter: In Progress
        $resInProg = $this->actingAs($this->produksi)->get(route('production-plans.index', ['status' => 'in_progress']));
        $resInProg->assertOk();
        $resInProg->assertSee('PLN-STATUS-INPROG');
        $resInProg->assertDontSee('PLN-STATUS-SCHED');
        $resInProg->assertDontSee('PLN-STATUS-COMPL');

        // Filter: Completed
        $resCompl = $this->actingAs($this->admin)->get(route('production-plans.index', ['status' => 'completed']));
        $resCompl->assertOk();
        $resCompl->assertSee('PLN-STATUS-COMPL');
        $resCompl->assertDontSee('PLN-STATUS-SCHED');
        $resCompl->assertDontSee('PLN-STATUS-INPROG');
    }

    /**
     * 8. Filter tanggal dan status dapat digunakan bersamaan.
     */
    public function test_filter_by_production_date_and_status_can_be_used_together(): void
    {
        // 1. Target: Cocok tanggal dan status
        $targetPlan = ProductionPlan::create([
            'plan_number' => 'PLN-MATCH-BOTH',
            'plan_date' => '2026-10-20',
            'production_date' => '2026-10-26',
            'status' => ProductionPlanStatus::Scheduled,
            'created_by' => $this->produksi->id,
        ]);
        $targetPlan->details()->create([
            'product_id' => $this->productA->id,
            'planned_quantity' => 50,
        ]);

        // 2. Beda status di tanggal yang sama
        $diffStatusPlan = ProductionPlan::create([
            'plan_number' => 'PLN-SAME-DATE-DIFF-STAT',
            'plan_date' => '2026-10-20',
            'production_date' => '2026-10-26',
            'status' => ProductionPlanStatus::InProgress,
            'created_by' => $this->produksi->id,
        ]);
        $diffStatusPlan->details()->create([
            'product_id' => $this->productA->id,
            'planned_quantity' => 40,
        ]);

        // 3. Status sama tapi tanggal berbeda
        $diffDatePlan = ProductionPlan::create([
            'plan_number' => 'PLN-DIFF-DATE-SAME-STAT',
            'plan_date' => '2026-10-20',
            'production_date' => '2026-10-27',
            'status' => ProductionPlanStatus::Scheduled,
            'created_by' => $this->produksi->id,
        ]);
        $diffDatePlan->details()->create([
            'product_id' => $this->productA->id,
            'planned_quantity' => 30,
        ]);

        $response = $this->actingAs($this->produksi)->get(route('production-plans.index', [
            'production_date' => '2026-10-26',
            'status' => 'scheduled',
        ]));

        $response->assertOk();
        $response->assertSee('PLN-MATCH-BOTH');
        $response->assertDontSee('PLN-SAME-DATE-DIFF-STAT');
        $response->assertDontSee('PLN-DIFF-DATE-SAME-STAT');
    }

    /**
     * 9. Kondisi data kosong ditampilkan dengan benar (belum ada rencana produksi).
     */
    public function test_empty_state_is_displayed_correctly(): void
    {
        $response = $this->actingAs($this->produksi)->get(route('production-plans.index'));

        $response->assertOk();
        $response->assertSee('Belum ada rencana produksi');
    }

    /**
     * Filter yang tidak menemukan hasil menampilkan pesan khusus dan tombol reset filter.
     */
    public function test_empty_state_is_displayed_when_filter_yields_no_results(): void
    {
        ProductionPlan::create([
            'plan_number' => 'PLN-EXISTING-001',
            'plan_date' => '2026-10-20',
            'production_date' => '2026-10-25',
            'status' => ProductionPlanStatus::Scheduled,
            'created_by' => $this->produksi->id,
        ]);

        $response = $this->actingAs($this->produksi)->get(route('production-plans.index', [
            'production_date' => '2026-10-30', // Tanggal tanpa rencana produksi
        ]));

        $response->assertOk();
        $response->assertSee('Tidak ada rencana produksi ditemukan');
        $response->assertSee('Reset Filter');
    }

    /**
     * Filter dengan format yang tidak valid memunculkan pesan peringatan.
     */
    public function test_invalid_filter_displays_validation_warning(): void
    {
        $response = $this->actingAs($this->produksi)->get(route('production-plans.index', [
            'production_date' => 'bukan-format-tanggal',
        ]));

        $response->assertOk();
        $response->assertSee('Format tanggal produksi tidak valid');
    }

    /**
     * Pagination mempertahankan query string filter.
     */
    public function test_pagination_preserves_filter_query_parameters(): void
    {
        for ($i = 1; $i <= 15; $i++) {
            $plan = ProductionPlan::create([
                'plan_number' => sprintf('PLN-PAGE-%04d', $i),
                'plan_date' => '2026-10-20',
                'production_date' => '2026-10-25',
                'status' => ProductionPlanStatus::Scheduled,
                'created_by' => $this->produksi->id,
            ]);
            $plan->details()->create([
                'product_id' => $this->productA->id,
                'planned_quantity' => 10,
            ]);
        }

        $response = $this->actingAs($this->produksi)->get(route('production-plans.index', [
            'production_date' => '2026-10-25',
            'status' => 'scheduled',
            'page' => 2,
        ]));

        $response->assertOk();
        $response->assertSee('production_date=2026-10-25');
        $response->assertSee('status=scheduled');
    }
}
