<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Jobs\ExportDataJob;
use App\Models\Customer;
use App\Models\Ingredient;
use App\Models\User;
use App\Services\ExportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ExportDataTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => UserRole::Admin]);
    }

    public function test_guest_cannot_access_export(): void
    {
        $response = $this->get(route('export.data', ['module' => 'customers', 'format' => 'excel']));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_export_customers_to_excel_csv(): void
    {
        Customer::factory()->create(['name' => 'Katering Sekolah Unggul']);

        $response = $this->actingAs($this->admin)->get(route('export.data', ['module' => 'customers', 'format' => 'excel']));

        $response->assertOk();
        $this->assertStringContainsString('text/csv', $response->headers->get('content-type'));
        $this->assertStringContainsString('data-pelanggan-', $response->headers->get('content-disposition'));
    }

    public function test_authenticated_user_can_view_pdf_printable_view(): void
    {
        Ingredient::factory()->create(['name' => 'Bawang Merah Brebes']);

        $response = $this->actingAs($this->admin)->get(route('export.data', ['module' => 'ingredients', 'format' => 'pdf']));

        $response->assertOk();
        $response->assertViewIs('exports.print');
        $response->assertSee('Laporan Rekapitulasi Stok Bahan Baku Gudang');
        $response->assertSee('Bawang Merah Brebes');
    }

    public function test_user_can_dispatch_export_to_background_queue_worker(): void
    {
        Queue::fake();

        $response = $this->actingAs($this->admin)->get(route('export.data', ['module' => 'customers', 'format' => 'queue']));

        $response->assertRedirect();
        Queue::assertPushed(ExportDataJob::class, function ($job) {
            return $job->module === 'customers' && $job->format === 'csv';
        });
    }

    public function test_export_data_job_executes_and_writes_file_to_storage(): void
    {
        Storage::fake('local');
        Customer::factory()->create(['name' => 'Rumah Sakit Bunda']);

        $job = new ExportDataJob('customers', 'csv', $this->admin->id);
        $job->handle(app(ExportService::class));

        $files = Storage::disk('local')->allFiles('exports');
        $this->assertNotEmpty($files);
        $content = Storage::disk('local')->get($files[0]);
        $this->assertStringContainsString('Rumah Sakit Bunda', $content);
    }
}
