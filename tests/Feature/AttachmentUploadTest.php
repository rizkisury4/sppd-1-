<?php

namespace Tests\Feature;

use App\Models\Sppd\SppdAttachment;
use App\Models\Sppd\SppdRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AttachmentUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_upload_and_delete_attachment(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $this->actingAs($user);

        $sppd = SppdRequest::create([
            'kode' => 'SPPD-UP-1',
            'pegawai_id' => $user->id,
            'tujuan' => 'Jakarta',
            'tanggal_berangkat' => now()->toDateString(),
            'tanggal_pulang' => now()->addDay()->toDateString(),
            'lama_hari' => 1,
            'maksud_perjalanan' => 'Rapat',
            'status' => 'draft',
        ]);

        $file = UploadedFile::fake()->image('bukti.png')->size(200);
        $resp = $this->post(route('sppd.attachments.store', $sppd), [
            'jenis' => 'bukti_biaya',
            'file' => $file,
        ]);
        $resp->assertRedirect(route('sppd.show', $sppd));

        $attachment = SppdAttachment::first();
        $this->assertNotNull($attachment);
        Storage::disk('public')->assertExists($attachment->path);

        $del = $this->delete(route('sppd.attachments.destroy', [$sppd, $attachment]));
        $del->assertRedirect(route('sppd.show', $sppd));

        Storage::disk('public')->assertMissing($attachment->path);
        $this->assertDatabaseMissing('sppd_attachments', ['id' => $attachment->id]);
    }
}

