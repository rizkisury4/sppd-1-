<?php

namespace Tests\Feature;

use App\Models\Sppd\SppdRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SppdIndexFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_filters_support_keyword_and_overlapping_date_range(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        SppdRequest::create([
            'kode' => 'SPPD-LIST-MATCH',
            'pegawai_id' => $admin->id,
            'tujuan' => 'Monitoring Surabaya',
            'kota' => 'Surabaya',
            'tanggal_berangkat' => '2026-05-01',
            'tanggal_pulang' => '2026-05-08',
            'lama_hari' => 7,
            'maksud_perjalanan' => 'Monitoring',
            'status' => 'diajukan',
        ]);
        SppdRequest::create([
            'kode' => 'SPPD-LIST-OTHER',
            'pegawai_id' => $admin->id,
            'tujuan' => 'Koordinasi Semarang',
            'kota' => 'Semarang',
            'tanggal_berangkat' => '2026-06-01',
            'tanggal_pulang' => '2026-06-02',
            'lama_hari' => 1,
            'maksud_perjalanan' => 'Koordinasi',
            'status' => 'disetujui',
        ]);

        $res = $this->get(route('sppd.index', [
            'q' => 'Surabaya',
            'status' => 'diajukan',
            'dari' => '2026-05-03',
            'sampai' => '2026-05-04',
        ]));

        $res->assertStatus(200);
        $res->assertSee('SPPD-LIST-MATCH');
        $res->assertDontSee('SPPD-LIST-OTHER');
    }
}