<?php

namespace Tests\Feature;

use App\Events\SppdApproved;
use App\Events\SppdRejected;
use App\Models\Sppd\SppdApproval;
use App\Models\Sppd\SppdRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Tests\TestCase;

class SppdWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_creates_draft_without_approval_trail(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $response = $this->post(route('sppd.store'), [
            'tujuan' => 'Palembang',
            'kota' => 'Palembang',
            'jenis_perjalanan' => 'non_diklat',
            'tanggal_berangkat' => now()->toDateString(),
            'tanggal_pulang' => now()->addDay()->toDateString(),
            'lama_hari' => 1,
            'maksud_perjalanan' => 'Rapat koordinasi',
            'status' => 'diajukan',
        ]);

        $sppd = SppdRequest::query()->latest('id')->firstOrFail();

        $response->assertRedirect(route('sppd.show', $sppd));
        $this->assertSame('draft', $sppd->status);
        $this->assertDatabaseMissing('sppd_approvals', ['sppd_id' => $sppd->id]);
    }

    public function test_ajukan_creates_approval_trail(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $this->actingAs($user);
        $sppd = SppdRequest::create([
            'kode' => 'SPPD-WF-1',
            'pegawai_id' => $user->id,
            'tujuan' => 'Medan',
            'tanggal_berangkat' => now()->toDateString(),
            'tanggal_pulang' => now()->addDay()->toDateString(),
            'lama_hari' => 1,
            'maksud_perjalanan' => 'Rapat',
            'status' => 'draft',
        ]);

        $resp = $this->post(route('sppd.ajukan', $sppd));
        $resp->assertRedirect(route('sppd.show', $sppd));
        $this->assertDatabaseHas('sppd_requests', ['id' => $sppd->id, 'status' => 'diajukan']);
        $this->assertDatabaseHas('sppd_approvals', ['sppd_id' => $sppd->id, 'status' => 'diajukan']);
    }

    public function test_setujui_triggers_event_and_trail(): void
    {
        Event::fake();
        $pegawai = User::factory()->create();
        $manager = User::factory()->create(['role' => 'manager']);
        $direksi = User::factory()->create(['role' => 'direksi']);
        $this->actingAs($pegawai);
        $sppd = SppdRequest::create([
            'kode' => 'SPPD-WF-2',
            'pegawai_id' => $pegawai->id,
            'tujuan' => 'Bali',
            'tanggal_berangkat' => now()->toDateString(),
            'tanggal_pulang' => now()->addDay()->toDateString(),
            'lama_hari' => 1,
            'maksud_perjalanan' => 'Rapat',
            'status' => 'diajukan',
        ]);

        $this->actingAs($manager);
        $resp = $this->post(route('sppd.setujui', $sppd));
        $resp->assertRedirect(route('sppd.show', $sppd));
        $this->assertDatabaseHas('sppd_requests', ['id' => $sppd->id, 'status' => 'disetujui_manager']);
        $this->assertDatabaseHas('sppd_approvals', [
            'sppd_id' => $sppd->id,
            'approver_id' => $manager->id,
            'status' => 'disetujui',
            'catatan' => 'Disetujui Manager',
        ]);
        Event::assertNotDispatched(SppdApproved::class);

        $this->actingAs($direksi);
        $resp = $this->post(route('sppd.setujui', $sppd->fresh()));
        $resp->assertRedirect(route('sppd.show', $sppd));
        $this->assertDatabaseHas('sppd_requests', ['id' => $sppd->id, 'status' => 'disetujui']);
        $this->assertDatabaseHas('sppd_approvals', [
            'sppd_id' => $sppd->id,
            'approver_id' => $direksi->id,
            'status' => 'disetujui',
            'catatan' => 'Disetujui Direksi',
        ]);
        Event::assertDispatched(SppdApproved::class);
    }

    public function test_tolak_triggers_event_and_trail(): void
    {
        Event::fake();
        $pegawai = User::factory()->create();
        $manager = User::factory()->create(['role' => 'manager']);
        $sppd = SppdRequest::create([
            'kode' => 'SPPD-WF-3',
            'pegawai_id' => $pegawai->id,
            'tujuan' => 'Makassar',
            'tanggal_berangkat' => now()->toDateString(),
            'tanggal_pulang' => now()->addDay()->toDateString(),
            'lama_hari' => 1,
            'maksud_perjalanan' => 'Rapat',
            'status' => 'diajukan',
        ]);

        $this->actingAs($manager);
        $resp = $this->post(route('sppd.tolak', $sppd), ['alasan_penolakan' => 'Budget tidak tersedia']);
        $resp->assertRedirect(route('sppd.show', $sppd));
        $this->assertDatabaseHas('sppd_requests', ['id' => $sppd->id, 'status' => 'ditolak']);
        $this->assertDatabaseHas('sppd_approvals', ['sppd_id' => $sppd->id, 'status' => 'ditolak']);
        Event::assertDispatched(SppdRejected::class);
    }

    public function test_pdf_redirects_back_when_sppd_is_incomplete(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $sppd = SppdRequest::create([
            'kode' => 'SPPD-WF-4',
            'pegawai_id' => $admin->id,
            'tujuan' => 'Jakarta',
            'kota' => 'Jakarta',
            'negara' => 'Indonesia',
            'jenis_perjalanan' => 'non_diklat',
            'tanggal_berangkat' => now()->toDateString(),
            'tanggal_pulang' => now()->addDay()->toDateString(),
            'lama_hari' => 1,
            'maksud_perjalanan' => 'Rapat',
            'status' => 'draft',
        ]);

        $response = $this->get(route('sppd.pdf', $sppd));

        $response->assertRedirect(route('sppd.show', $sppd));
        $response->assertSessionHasErrors('pdf');
    }

    public function test_pdf_shows_manager_signatures_before_direksi_approval(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'name' => 'Admin Utama']);
        $manager = User::factory()->create(['role' => 'manager', 'name' => 'Manager Area']);

        $sppd = SppdRequest::create([
            'kode' => 'SPPD-WF-5',
            'pegawai_id' => $admin->id,
            'tujuan' => 'Bandung',
            'kota' => 'Bandung',
            'negara' => 'Indonesia',
            'jenis_perjalanan' => 'non_diklat',
            'jenis_surat' => 'surat_tugas',
            'sumber_anggaran' => 'Anggaran internal',
            'transportasi' => 'Mobil dinas',
            'pejabat_perintah_id' => $manager->id,
            'tanggal_berangkat' => now()->toDateString(),
            'tanggal_pulang' => now()->addDay()->toDateString(),
            'lama_hari' => 1,
            'maksud_perjalanan' => 'Rapat',
            'status' => 'disetujui_manager',
            'anggota' => ['Admin Utama'],
        ]);

        $sppd->approvals()->create([
            'approver_id' => $manager->id,
            'status' => 'disetujui',
            'catatan' => 'Disetujui Manager',
            'acted_at' => now(),
        ]);

        $html = view('sppd.pdf', ['sppd' => $sppd->load(['expenses', 'attachments', 'approvals.approver', 'pegawai', 'pejabatPerintah'])])->render();

        $this->assertTrue(Str::contains($html, 'Manager'));
        $this->assertEquals(2, substr_count($html, '<div class="sign-role">Manager</div>'));
        $this->assertFalse(Str::contains($html, '<div class="sign-role">Admin</div>'));
        $this->assertFalse(Str::contains($html, '<div class="sign-role">Direksi</div>'));
        $this->assertTrue(Str::contains($html, 'Manager Area'));
    }

    public function test_pdf_shows_admin_and_direksi_signatures_after_direksi_approval(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'name' => 'Admin Utama']);
        $manager = User::factory()->create(['role' => 'manager', 'name' => 'Manager Area']);
        $direksi = User::factory()->create(['role' => 'direksi', 'name' => 'Direksi Utama']);

        $sppd = SppdRequest::create([
            'kode' => 'SPPD-WF-6',
            'pegawai_id' => $admin->id,
            'tujuan' => 'Semarang',
            'kota' => 'Semarang',
            'negara' => 'Indonesia',
            'jenis_perjalanan' => 'non_diklat',
            'jenis_surat' => 'surat_tugas',
            'sumber_anggaran' => 'Anggaran internal',
            'transportasi' => 'Pesawat',
            'pejabat_perintah_id' => $manager->id,
            'tanggal_berangkat' => now()->toDateString(),
            'tanggal_pulang' => now()->addDay()->toDateString(),
            'lama_hari' => 1,
            'maksud_perjalanan' => 'Koordinasi',
            'status' => 'disetujui',
            'anggota' => ['Admin Utama'],
        ]);

        $sppd->approvals()->create([
            'approver_id' => $manager->id,
            'status' => 'disetujui',
            'catatan' => 'Disetujui Manager',
            'acted_at' => now(),
        ]);

        $sppd->approvals()->create([
            'approver_id' => $direksi->id,
            'status' => 'disetujui',
            'catatan' => 'Disetujui Direksi',
            'acted_at' => now(),
        ]);

        $html = view('sppd.pdf', ['sppd' => $sppd->load(['expenses', 'attachments', 'approvals.approver', 'pegawai', 'pejabatPerintah'])])->render();

        $this->assertTrue(Str::contains($html, 'Admin'));
        $this->assertTrue(Str::contains($html, 'Direksi'));
        $this->assertFalse(Str::contains($html, '<div class="sign-role">Manager</div>'));
        $this->assertTrue(Str::contains($html, 'Direksi Utama'));
    }
}

