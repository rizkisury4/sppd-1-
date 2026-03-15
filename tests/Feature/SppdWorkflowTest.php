<?php

namespace Tests\Feature;

use App\Events\SppdApproved;
use App\Events\SppdRejected;
use App\Models\Sppd\SppdApproval;
use App\Models\Sppd\SppdRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class SppdWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_ajukan_creates_approval_trail(): void
    {
        $user = User::factory()->create();
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
        $this->assertDatabaseHas('sppd_requests', ['id' => $sppd->id, 'status' => 'disetujui']);
        $this->assertDatabaseHas('sppd_approvals', ['sppd_id' => $sppd->id, 'status' => 'disetujui']);
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
}

