<?php

namespace Tests\Feature;

use App\Models\Sppd\SppdExpense;
use App\Models\Sppd\SppdRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportRekapTest extends TestCase
{
    use RefreshDatabase;

    public function test_rekap_aggregates_by_category(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $s1 = SppdRequest::create([
            'kode' => 'R1',
            'pegawai_id' => $admin->id,
            'tujuan' => 'A',
            'tanggal_berangkat' => now()->toDateString(),
            'tanggal_pulang' => now()->addDay()->toDateString(),
            'lama_hari' => 1,
            'maksud_perjalanan' => 'X',
            'status' => 'disetujui',
        ]);
        $s2 = SppdRequest::create([
            'kode' => 'R2',
            'pegawai_id' => $admin->id,
            'tujuan' => 'B',
            'tanggal_berangkat' => now()->toDateString(),
            'tanggal_pulang' => now()->addDay()->toDateString(),
            'lama_hari' => 1,
            'maksud_perjalanan' => 'Y',
            'status' => 'disetujui',
        ]);

        SppdExpense::create(['sppd_id' => $s1->id, 'kategori' => 'transport', 'jumlah' => 100, 'tanggal' => now()->toDateString()]);
        SppdExpense::create(['sppd_id' => $s1->id, 'kategori' => 'harian', 'jumlah' => 50, 'tanggal' => now()->toDateString()]);
        SppdExpense::create(['sppd_id' => $s2->id, 'kategori' => 'transport', 'jumlah' => 200, 'tanggal' => now()->toDateString()]);

        $res = $this->get(route('sppd.rekap', ['status' => 'disetujui']));
        $res->assertStatus(200);
        $res->assertSee('Total Perjalanan');
        $res->assertSee('Transport');
        $res->assertSee('Harian');
    }

    public function test_rekap_csv_export(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);
        $res = $this->get(route('sppd.rekap', ['export' => 'csv']));
        $res->assertStatus(200);
        $res->assertHeader('content-type', 'text/csv; charset=utf-8');
    }
}
