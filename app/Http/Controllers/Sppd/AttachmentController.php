<?php

namespace App\Http\Controllers\Sppd;

use App\Http\Controllers\Controller;
use App\Models\Sppd\SppdAttachment;
use App\Models\Sppd\SppdRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AttachmentController extends Controller
{
    public function store(Request $request, SppdRequest $sppd): RedirectResponse
    {
        $this->authorize('update', $sppd);

        $data = $request->validate([
            'jenis' => ['required','string','in:surat_tugas,tiket,bukti_biaya,lainnya'],
            'file' => ['required','file','max:4096','mimes:pdf,jpg,jpeg,png,webp'],
        ]);

        $disk = 'public';
        $dir = 'attachments/sppd/'.$sppd->id;
        $path = $request->file('file')->store($dir, $disk);
        $mime = $request->file('file')->getClientMimeType();
        $size = $request->file('file')->getSize();

        $sppd->attachments()->create([
            'jenis' => $data['jenis'],
            'path' => $path,
            'ukuran' => $size,
            'mime' => $mime,
            'uploaded_by' => Auth::id(),
            'uploaded_at' => now(),
        ]);

        return redirect()->route('sppd.show', $sppd);
    }

    public function destroy(SppdRequest $sppd, SppdAttachment $attachment): RedirectResponse
    {
        $this->authorize('update', $sppd);
        if ($attachment->sppd_id !== $sppd->id) {
            abort(404);
        }

        if ($attachment->path) {
            Storage::disk('public')->delete($attachment->path);
        }

        $attachment->delete();
        return redirect()->route('sppd.show', $sppd);
    }
}

