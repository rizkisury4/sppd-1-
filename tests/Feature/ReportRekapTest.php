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
        $res->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    public function test_rekap_excel_export(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $res = $this->get(route('sppd.rekap', ['export' => 'excel']));

        $res->assertStatus(200);
        $res->assertHeader('content-type', 'application/vnd.ms-excel; charset=UTF-8');
    }

    public function test_rekap_filters_support_keyword_and_overlapping_date_range(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $matching = SppdRequest::create([
            'kode' => 'SPPD-MATCH',
            'pegawai_id' => $admin->id,
            'tujuan' => 'Pelatihan Bandung',
            'kota' => 'Bandung',
            'tanggal_berangkat' => '2026-05-01',
            'tanggal_pulang' => '2026-05-10',
            'lama_hari' => 9,
            'maksud_perjalanan' => 'Pelatihan',
            'status' => 'disetujui_manager',
        ]);
        $nonMatching = SppdRequest::create([
            'kode' => 'SPPD-OTHER',
            'pegawai_id' => $admin->id,
            'tujuan' => 'Rapat Jakarta',
            'kota' => 'Jakarta',
            'tanggal_berangkat' => '2026-06-01',
            'tanggal_pulang' => '2026-06-02',
            'lama_hari' => 1,
            'maksud_perjalanan' => 'Rapat',
            'status' => 'disetujui',
        ]);

        SppdExpense::create(['sppd_id' => $matching->id, 'kategori' => 'transport', 'jumlah' => 100, 'tanggal' => now()->toDateString()]);
        SppdExpense::create(['sppd_id' => $nonMatching->id, 'kategori' => 'transport', 'jumlah' => 150, 'tanggal' => now()->toDateString()]);

        $res = $this->get(route('sppd.rekap', [
            'q' => 'Bandung',
            'status' => 'disetujui_manager',
            'dari' => '2026-05-05',
            'sampai' => '2026-05-06',
        ]));

        $res->assertStatus(200);
        $res->assertSee('SPPD-MATCH');
        $res->assertDontSee('SPPD-OTHER');
    }
}
