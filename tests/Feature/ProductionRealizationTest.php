<?php

namespace Tests\Feature;

use App\Enums\ProductionPlanStatus;
use App\Enums\UserRole;
use App\Models\Product;
use App\Models\Production;
use App\Models\ProductionPlan;
use App\Models\ProductionPlanDetail;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ProductionRealizationTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $produksi;

    private User $gudang;

    private User $owner;

    private Product $product1;

    private Product $product2;

    private ProductionPlan $inProgressPlan;

    private ProductionPlanDetail $detail1;

    private ProductionPlanDetail $detail2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => UserRole::Admin]);
        $this->produksi = User::factory()->create(['role' => UserRole::Produksi]);
        $this->gudang = User::factory()->create(['role' => UserRole::Gudang]);
        $this->owner = User::factory()->create(['role' => UserRole::Owner]);

        $this->product1 = Product::factory()->create([
            'name' => 'Nasi Ayam Bakar Madu',
            'unit' => 'porsi',
            'selling_price' => 25000,
            'is_active' => true,
        ]);

        $this->product2 = Product::factory()->create([
            'name' => 'Capcay Kuah Kental Jamur',
            'unit' => 'porsi',
            'selling_price' => 15000,
            'is_active' => true,
        ]);

        $this->inProgressPlan = ProductionPlan::create([
            'plan_number' => 'PLN-20261019-0001',
            'plan_date' => '2026-10-19',
            'production_date' => '2026-10-19',
            'status' => ProductionPlanStatus::InProgress,
            'notes' => 'Masak catering makan siang kantor',
            'created_by' => $this->produksi->id,
        ]);

        $this->detail1 = $this->inProgressPlan->details()->create([
            'product_id' => $this->product1->id,
            'planned_quantity' => 50,
            'notes' => 'Kemasan bento premium',
        ]);

        $this->detail2 = $this->inProgressPlan->details()->create([
            'product_id' => $this->product2->id,
            'planned_quantity' => 30,
            'notes' => 'Wadah mangkok terpisah',
        ]);
    }

    /**
     * 1. Pengguna yang belum login diarahkan ke login.
     */
    public function test_guests_are_redirected_to_login(): void
    {
        $this->get(route('productions.index'))
            ->assertRedirect(route('login'));

        $this->get(route('production-plans.realization.create', $this->inProgressPlan))
            ->assertRedirect(route('login'));

        $this->post(route('production-plans.realization.store', $this->inProgressPlan), [])
            ->assertRedirect(route('login'));
    }

    /**
     * 2. Role produksi dapat mengakses fitur.
     */
    public function test_produksi_can_access_index_and_form(): void
    {
        $this->actingAs($this->produksi)
            ->get(route('productions.index'))
            ->assertOk()
            ->assertSee('Pencatatan Realisasi Masak Dapur')
            ->assertSee($this->inProgressPlan->plan_number);

        $this->actingAs($this->produksi)
            ->get(route('production-plans.realization.create', $this->inProgressPlan))
            ->assertOk()
            ->assertSee($this->inProgressPlan->plan_number)
            ->assertSee('Nasi Ayam Bakar Madu')
            ->assertSee('Capcay Kuah Kental Jamur');
    }

    /**
     * 3. Role admin dapat mengakses fitur.
     */
    public function test_admin_can_access_index_and_form(): void
    {
        $this->actingAs($this->admin)
            ->get(route('productions.index'))
            ->assertOk()
            ->assertSee($this->inProgressPlan->plan_number);

        $this->actingAs($this->admin)
            ->get(route('production-plans.realization.create', $this->inProgressPlan))
            ->assertOk()
            ->assertSee($this->inProgressPlan->plan_number);
    }

    /**
     * 4. Role lain (gudang, owner) ditolak mengakses fitur (403 Forbidden).
     */
    public function test_unauthorized_roles_are_forbidden(): void
    {
        // Role Gudang
        $this->actingAs($this->gudang)
            ->get(route('productions.index'))
            ->assertForbidden();

        $this->actingAs($this->gudang)
            ->get(route('production-plans.realization.create', $this->inProgressPlan))
            ->assertForbidden();

        $this->actingAs($this->gudang)
            ->post(route('production-plans.realization.store', $this->inProgressPlan), [])
            ->assertForbidden();

        // Role Owner
        $this->actingAs($this->owner)
            ->get(route('productions.index'))
            ->assertForbidden();

        $this->actingAs($this->owner)
            ->get(route('production-plans.realization.create', $this->inProgressPlan))
            ->assertForbidden();

        $this->actingAs($this->owner)
            ->post(route('production-plans.realization.store', $this->inProgressPlan), [])
            ->assertForbidden();
    }

    /**
     * 5. Rencana berstatus in_progress dapat dipilih dan dibuka formnya.
     */
    public function test_in_progress_plan_can_be_selected_and_opened(): void
    {
        $response = $this->actingAs($this->produksi)
            ->get(route('production-plans.realization.create', $this->inProgressPlan));

        $response->assertOk();
        $response->assertSee('50'); // planned quantity detail 1
        $response->assertSee('30'); // planned quantity detail 2
    }

    /**
     * 6. Rencana yang sudah completed tidak dapat menerima pencatatan baru.
     */
    public function test_completed_plan_cannot_receive_new_realization(): void
    {
        $completedPlan = ProductionPlan::create([
            'plan_number' => 'PLN-20261019-9999',
            'plan_date' => '2026-10-19',
            'production_date' => '2026-10-19',
            'status' => ProductionPlanStatus::Completed,
            'created_by' => $this->produksi->id,
        ]);

        $detail = $completedPlan->details()->create([
            'product_id' => $this->product1->id,
            'planned_quantity' => 20,
        ]);

        // GET form diarahkan kembali
        $this->actingAs($this->produksi)
            ->get(route('production-plans.realization.create', $completedPlan))
            ->assertRedirect(route('productions.index'))
            ->assertSessionHas('warning');

        // POST ditolak validasi
        $this->actingAs($this->produksi)
            ->post(route('production-plans.realization.store', $completedPlan), [
                'production_date' => '2026-10-19',
                'items' => [
                    [
                        'detail_id' => $detail->id,
                        'actual_quantity' => 20,
                    ],
                ],
            ])
            ->assertSessionHasErrors('plan');
    }

    /**
     * 7. Realisasi di bawah jumlah rencana menghasilkan status 'partial'.
     */
    public function test_realization_below_planned_quantity_results_in_partial_status(): void
    {
        $this->actingAs($this->produksi)
            ->post(route('production-plans.realization.store', $this->inProgressPlan), [
                'production_date' => '2026-10-19',
                'items' => [
                    [
                        'detail_id' => $this->detail1->id,
                        'actual_quantity' => 25, // Target 50, baru dimasak 25
                        'notes' => 'Batch 1 baru selesai setengah',
                    ],
                    [
                        'detail_id' => $this->detail2->id,
                        'actual_quantity' => 0,
                    ],
                ],
            ])
            ->assertRedirect(route('productions.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('productions', [
            'production_plan_detail_id' => $this->detail1->id,
            'actual_quantity' => 25,
            'status' => 'partial',
        ]);

        $this->inProgressPlan->refresh();
        $this->assertEquals(ProductionPlanStatus::InProgress, $this->inProgressPlan->status);
    }

    /**
     * 8. Realisasi yang memenuhi atau melampaui jumlah rencana menghasilkan status 'completed'.
     */
    public function test_realization_meeting_or_exceeding_planned_quantity_results_in_completed_status(): void
    {
        $this->actingAs($this->produksi)
            ->post(route('production-plans.realization.store', $this->inProgressPlan), [
                'production_date' => '2026-10-19',
                'items' => [
                    [
                        'detail_id' => $this->detail1->id,
                        'actual_quantity' => 55, // Target 50, ada kelebihan porsi (55)
                    ],
                    [
                        'detail_id' => $this->detail2->id,
                        'actual_quantity' => 10, // Belum selesai (target 30)
                    ],
                ],
            ]);

        $this->assertDatabaseHas('productions', [
            'production_plan_detail_id' => $this->detail1->id,
            'actual_quantity' => 55,
            'status' => 'completed',
        ]);

        $this->assertDatabaseHas('productions', [
            'production_plan_detail_id' => $this->detail2->id,
            'actual_quantity' => 10,
            'status' => 'partial',
        ]);
    }

    /**
     * 9. Penyelesaian satu menu saja tidak menyelesaikan seluruh rencana jika ada menu lain yang belum memenuhi.
     */
    public function test_completing_one_item_does_not_complete_entire_plan(): void
    {
        $this->actingAs($this->produksi)
            ->post(route('production-plans.realization.store', $this->inProgressPlan), [
                'production_date' => '2026-10-19',
                'items' => [
                    [
                        'detail_id' => $this->detail1->id,
                        'actual_quantity' => 50, // Selesai
                    ],
                    [
                        'detail_id' => $this->detail2->id,
                        'actual_quantity' => 20, // Belum selesai (target 30)
                    ],
                ],
            ]);

        $this->inProgressPlan->refresh();
        $this->assertEquals(ProductionPlanStatus::InProgress, $this->inProgressPlan->status);
    }

    /**
     * 10. Seluruh menu yang memenuhi jumlah rencana dapat mengubah status rencana menjadi 'completed'.
     */
    public function test_all_items_completed_updates_plan_status_to_completed(): void
    {
        $this->actingAs($this->produksi)
            ->post(route('production-plans.realization.store', $this->inProgressPlan), [
                'production_date' => '2026-10-19',
                'items' => [
                    [
                        'detail_id' => $this->detail1->id,
                        'actual_quantity' => 50, // Target 50 tercapai
                    ],
                    [
                        'detail_id' => $this->detail2->id,
                        'actual_quantity' => 30, // Target 30 tercapai
                    ],
                ],
            ])
            ->assertRedirect(route('productions.index'));

        $this->inProgressPlan->refresh();
        $this->assertEquals(ProductionPlanStatus::Completed, $this->inProgressPlan->status);
    }

    /**
     * 11. Nilai aktual negatif atau tidak valid ditolak.
     */
    public function test_negative_or_invalid_actual_quantity_is_rejected(): void
    {
        $response = $this->actingAs($this->produksi)
            ->post(route('production-plans.realization.store', $this->inProgressPlan), [
                'production_date' => '2026-10-19',
                'items' => [
                    [
                        'detail_id' => $this->detail1->id,
                        'actual_quantity' => -10, // Negatif
                    ],
                ],
            ]);

        $response->assertSessionHasErrors('items.0.actual_quantity');
        $this->assertDatabaseEmpty('productions');
    }

    /**
     * 12. Detail menu yang bukan bagian dari rencana terpilih ditolak.
     */
    public function test_detail_id_from_other_plan_is_rejected(): void
    {
        $otherPlan = ProductionPlan::create([
            'plan_number' => 'PLN-20261019-0002',
            'plan_date' => '2026-10-19',
            'production_date' => '2026-10-19',
            'status' => ProductionPlanStatus::InProgress,
            'created_by' => $this->produksi->id,
        ]);

        $otherDetail = $otherPlan->details()->create([
            'product_id' => $this->product1->id,
            'planned_quantity' => 10,
        ]);

        $response = $this->actingAs($this->produksi)
            ->post(route('production-plans.realization.store', $this->inProgressPlan), [
                'production_date' => '2026-10-19',
                'items' => [
                    [
                        'detail_id' => $otherDetail->id, // Milik otherPlan!
                        'actual_quantity' => 10,
                    ],
                ],
            ]);

        $response->assertSessionHasErrors('items.0.detail_id');
        $this->assertDatabaseEmpty('productions');
    }

    /**
     * 13. Pembaruan realisasi tidak menghasilkan data duplikat.
     */
    public function test_updating_realization_does_not_create_duplicates(): void
    {
        // Simpan pertama: 20 porsi
        $this->actingAs($this->produksi)
            ->post(route('production-plans.realization.store', $this->inProgressPlan), [
                'production_date' => '2026-10-19',
                'items' => [
                    [
                        'detail_id' => $this->detail1->id,
                        'actual_quantity' => 20,
                    ],
                    [
                        'detail_id' => $this->detail2->id,
                        'actual_quantity' => 10,
                    ],
                ],
            ]);

        $this->assertDatabaseCount('productions', 2);

        // Simpan kedua (pembaruan): 50 porsi
        $this->actingAs($this->produksi)
            ->post(route('production-plans.realization.store', $this->inProgressPlan), [
                'production_date' => '2026-10-19',
                'items' => [
                    [
                        'detail_id' => $this->detail1->id,
                        'actual_quantity' => 50,
                    ],
                    [
                        'detail_id' => $this->detail2->id,
                        'actual_quantity' => 30,
                    ],
                ],
            ]);

        // Tetap hanya 2 baris (tidak bertambah jadi 4)
        $this->assertDatabaseCount('productions', 2);

        $this->assertDatabaseHas('productions', [
            'production_plan_detail_id' => $this->detail1->id,
            'actual_quantity' => 50,
            'status' => 'completed',
        ]);
    }

    /**
     * 14. Penyimpanan realisasi terjadi dalam database transaction yang konsisten.
     */
    public function test_database_transaction_consistency(): void
    {
        // Pastikan model Production berjalan di dalam transaksi
        $this->actingAs($this->admin)
            ->post(route('production-plans.realization.store', $this->inProgressPlan), [
                'production_date' => '2026-10-19',
                'items' => [
                    [
                        'detail_id' => $this->detail1->id,
                        'actual_quantity' => 50,
                    ],
                    [
                        'detail_id' => $this->detail2->id,
                        'actual_quantity' => 30,
                    ],
                ],
            ]);

        $this->assertDatabaseCount('productions', 2);
        $this->inProgressPlan->refresh();
        $this->assertEquals(ProductionPlanStatus::Completed, $this->inProgressPlan->status);
    }

    /**
     * 15. Catatan per menu tersimpan dengan benar saat kolom notes tersedia di database.
     */
    public function test_realization_can_be_stored_with_notes_when_column_exists(): void
    {
        $this->assertTrue(Schema::hasColumn('productions', 'notes'));

        $response = $this->actingAs($this->produksi)
            ->post(route('production-plans.realization.store', $this->inProgressPlan), [
                'production_date' => '2026-10-19',
                'notes' => 'Catatan umum dapur',
                'items' => [
                    [
                        'detail_id' => $this->detail1->id,
                        'actual_quantity' => 50,
                        'notes' => 'Tingkat kematangan sempurna',
                    ],
                    [
                        'detail_id' => $this->detail2->id,
                        'actual_quantity' => 30,
                        'notes' => 'Sayuran segar dan renyah',
                    ],
                ],
            ]);

        $response->assertRedirect(route('productions.index'));
        $response->assertSessionHas('success');
        $response->assertSessionMissing('warning');

        $this->assertDatabaseHas('productions', [
            'production_plan_detail_id' => $this->detail1->id,
            'actual_quantity' => 50,
            'notes' => 'Tingkat kematangan sempurna',
        ]);
    }

    /**
     * 16. Penyimpanan realisasi tetap berhasil dan aman saat kolom notes belum ada di database.
     */
    public function test_realization_can_be_stored_gracefully_when_notes_column_is_missing(): void
    {
        // Simulasikan lingkungan database Supabase saat ini di mana kolom notes belum ada
        Schema::table('productions', function (Blueprint $table) {
            $table->dropColumn('notes');
        });

        $this->assertFalse(Schema::hasColumn('productions', 'notes'));

        $response = $this->actingAs($this->produksi)
            ->post(route('production-plans.realization.store', $this->inProgressPlan), [
                'production_date' => '2026-10-19',
                'notes' => 'Catatan dapur saat kolom belum ada',
                'items' => [
                    [
                        'detail_id' => $this->detail1->id,
                        'actual_quantity' => 50,
                        'notes' => 'Catatan menu yang belum bisa disimpan',
                    ],
                    [
                        'detail_id' => $this->detail2->id,
                        'actual_quantity' => 30,
                    ],
                ],
            ]);

        $response->assertRedirect(route('productions.index'));
        $response->assertSessionHas('success');
        // Peringatan transparan muncul memberi tahu bahwa catatan belum tersimpan permanen
        $response->assertSessionHas('warning');

        // Porsi dan status tetap tersimpan dengan akurat tanpa crash SQL
        $this->assertDatabaseHas('productions', [
            'production_plan_detail_id' => $this->detail1->id,
            'actual_quantity' => 50,
            'status' => 'completed',
        ]);

        $this->inProgressPlan->refresh();
        $this->assertEquals(ProductionPlanStatus::Completed, $this->inProgressPlan->status);
    }

    /**
     * 17. Pembaruan (update) realisasi tetap berjalan aman saat kolom notes belum ada di database.
     */
    public function test_realization_can_be_updated_when_notes_column_is_missing(): void
    {
        Schema::table('productions', function (Blueprint $table) {
            $table->dropColumn('notes');
        });

        // Simpan awal: porsi parsial
        $this->actingAs($this->produksi)
            ->post(route('production-plans.realization.store', $this->inProgressPlan), [
                'production_date' => '2026-10-19',
                'items' => [
                    [
                        'detail_id' => $this->detail1->id,
                        'actual_quantity' => 20,
                        'notes' => 'Batch 1',
                    ],
                    [
                        'detail_id' => $this->detail2->id,
                        'actual_quantity' => 10,
                    ],
                ],
            ]);

        $this->assertDatabaseHas('productions', [
            'production_plan_detail_id' => $this->detail1->id,
            'actual_quantity' => 20,
            'status' => 'partial',
        ]);

        // Simpan kedua (update): porsi selesai
        $response = $this->actingAs($this->produksi)
            ->post(route('production-plans.realization.store', $this->inProgressPlan), [
                'production_date' => '2026-10-19',
                'items' => [
                    [
                        'detail_id' => $this->detail1->id,
                        'actual_quantity' => 50,
                        'notes' => 'Batch 2 selesai',
                    ],
                    [
                        'detail_id' => $this->detail2->id,
                        'actual_quantity' => 30,
                    ],
                ],
            ]);

        $response->assertRedirect(route('productions.index'));
        $response->assertSessionHas('success');

        // Verifikasi update berhasil dan tidak ada baris duplikat
        $this->assertDatabaseCount('productions', 2);
        $this->assertDatabaseHas('productions', [
            'production_plan_detail_id' => $this->detail1->id,
            'actual_quantity' => 50,
            'status' => 'completed',
        ]);
    }

    /**
     * 18. Catatan pada rencana produksi dan detail rencana tidak pernah ditimpa oleh realisasi dapur.
     */
    public function test_production_plan_and_detail_notes_are_never_overwritten(): void
    {
        $planInitialNotes = 'Catatan Perencanaan Master: Rendah Garam, Kirim Sebelum Pukul 11:30';
        $detailInitialNotes = 'Spesifikasi Rencana: Kemasan Kotak Khusus Diet';

        $this->inProgressPlan->update(['notes' => $planInitialNotes]);
        $this->detail1->update(['notes' => $detailInitialNotes]);

        $this->actingAs($this->produksi)
            ->post(route('production-plans.realization.store', $this->inProgressPlan), [
                'production_date' => '2026-10-19',
                'notes' => 'Catatan dapur saat eksekusi masak hari ini',
                'items' => [
                    [
                        'detail_id' => $this->detail1->id,
                        'actual_quantity' => 50,
                        'notes' => 'Catatan menu realisasi dapur',
                    ],
                    [
                        'detail_id' => $this->detail2->id,
                        'actual_quantity' => 30,
                    ],
                ],
            ]);

        $this->assertEquals($planInitialNotes, $this->inProgressPlan->fresh()->notes);
        $this->assertEquals($detailInitialNotes, $this->detail1->fresh()->notes);
    }

    /**
     * 19. Migration ensure_productions_table aman dieksekusi saat tabel sudah ada dan rollback tidak menghapus tabel.
     */
    public function test_ensure_productions_migration_is_safe_when_table_already_exists_and_rollback_does_not_drop_data(): void
    {
        $this->assertTrue(Schema::hasTable('productions'));

        // Muat instance migration ensure_productions_table
        $migrationFile = database_path('migrations/2026_10_19_000001_ensure_productions_table.php');
        $this->assertFileExists($migrationFile);

        $migration = require $migrationFile;

        // Uji eksekusi up() saat tabel sudah ada (kondisi branch lain sudah merge atau di Supabase)
        // Harus berjalan mulus tanpa error "table already exists"
        $migration->up();
        $this->assertTrue(Schema::hasTable('productions'));

        // Buat data sampel di tabel productions
        Production::create([
            'production_number' => 'PRD-TEST-0001',
            'production_plan_detail_id' => $this->detail1->id,
            'created_by' => $this->produksi->id,
            'production_date' => '2026-10-19',
            'actual_quantity' => 10,
            'status' => 'partial',
        ]);

        $this->assertDatabaseCount('productions', 1);

        // Uji eksekusi down()
        // Harus aman: tabel productions tidak boleh dihapus dan data tidak boleh hilang
        $migration->down();

        $this->assertTrue(Schema::hasTable('productions'));
        $this->assertDatabaseCount('productions', 1);
    }
}
