<?php

namespace Tests\Feature;

use App\Models\Sppd\SppdApproval;
use App\Models\Sppd\SppdAttachment;
use App\Models\Sppd\SppdExpense;
use App\Models\Sppd\SppdRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SppdSchemaTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_sppd_request_and_relations(): void
    {
        $pegawai = User::factory()->create();
        $manager = User::factory()->create(['role' => 'manager']);

        $sppd = SppdRequest::create([
            'kode' => 'SPPD-0001',
            'pegawai_id' => $pegawai->id,
            'tujuan' => 'Jakarta',
            'kota' => 'Jakarta',
            'negara' => 'ID',
            'tanggal_berangkat' => now()->toDateString(),
            'tanggal_pulang' => now()->addDays(2)->toDateString(),
            'lama_hari' => 2,
            'maksud_perjalanan' => 'Rapat dengan klien',
            'status' => 'diajukan',
        ]);

        $sppd->expenses()->create([
            'kategori' => 'transport',
            'deskripsi' => 'Taksi bandara',
            'jumlah' => 150000,
            'mata_uang' => 'IDR',
            'tanggal' => now()->toDateString(),
        ]);

        $sppd->attachments()->create([
            'jenis' => 'surat_tugas',
            'path' => 'attachments/surat_tugas.pdf',
            'ukuran' => 1024,
            'mime' => 'application/pdf',
            'uploaded_by' => $pegawai->id,
            'uploaded_at' => now(),
        ]);

        $sppd->approvals()->create([
            'approver_id' => $manager->id,
            'status' => 'diajukan',
            'catatan' => null,
            'acted_at' => null,
        ]);

        $this->assertDatabaseHas('sppd_requests', ['id' => $sppd->id, 'kode' => 'SPPD-0001']);
        $this->assertEquals(1, $sppd->expenses()->count());
        $this->assertEquals(1, $sppd->attachments()->count());
        $this->assertEquals(1, $sppd->approvals()->count());
    }

    public function test_delete_request_cascades_children(): void
    {
        $pegawai = User::factory()->create();

        $sppd = SppdRequest::create([
            'kode' => 'SPPD-0002',
            'pegawai_id' => $pegawai->id,
            'tujuan' => 'Bandung',
            'tanggal_berangkat' => now()->toDateString(),
            'tanggal_pulang' => now()->addDay()->toDateString(),
            'lama_hari' => 1,
            'maksud_perjalanan' => 'Survey lokasi',
            'status' => 'draft',
        ]);

        $sppd->expenses()->create([
            'kategori' => 'harian',
            'deskripsi' => 'Uang makan',
            'jumlah' => 50000,
            'mata_uang' => 'IDR',
            'tanggal' => now()->toDateString(),
        ]);

        $sppd->attachments()->create([
            'jenis' => 'bukti_biaya',
            'path' => 'attachments/bukti.jpg',
        ]);

        $sppdId = $sppd->id;
        $sppd->delete();

        $this->assertDatabaseMissing('sppd_requests', ['id' => $sppdId]);
        $this->assertDatabaseCount('sppd_expenses', 0);
        $this->assertDatabaseCount('sppd_attachments', 0);
    }

    public function test_null_on_delete_approver(): void
    {
        $pegawai = User::factory()->create();
        $approver = User::factory()->create();

        $sppd = SppdRequest::create([
            'kode' => 'SPPD-0003',
            'pegawai_id' => $pegawai->id,
            'tujuan' => 'Surabaya',
            'tanggal_berangkat' => now()->toDateString(),
            'tanggal_pulang' => now()->addDay()->toDateString(),
            'lama_hari' => 1,
            'maksud_perjalanan' => 'Pelatihan',
            'status' => 'diajukan',
        ]);

        $approval = $sppd->approvals()->create([
            'approver_id' => $approver->id,
            'status' => 'diajukan',
        ]);

        $approver->delete();
        $approval->refresh();

        $this->assertNull($approval->approver_id);
    }
}

