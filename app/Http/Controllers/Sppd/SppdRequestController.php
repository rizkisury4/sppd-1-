<?php

namespace App\Http\Controllers\Sppd;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sppd\StoreSppdRequest;
use App\Http\Requests\Sppd\UpdateSppdRequest;
use App\Events\SppdApproved;
use App\Events\SppdRejected;
use App\Models\Sppd\SppdRequest;
use App\Models\Sppd\SppdApproval;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SppdRequestController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $q = SppdRequest::query();
        if ($user->role === 'pegawai') {
            $q->where('pegawai_id', $user->id);
        }
        if (in_array($user->role, ['manager','admin','finance']) && $request->filled('pegawai_id')) {
            $q->where('pegawai_id', $request->integer('pegawai_id'));
        }
        if ($request->filled('status')) {
            $q->where('status', $request->string('status'));
        }
        $from = $request->date('dari');
        $to = $request->date('sampai');
        if ($from && $to) {
            $q->where(function ($w) use ($from, $to) {
                $w->whereBetween('tanggal_berangkat', [$from, $to])
                  ->orWhereBetween('tanggal_pulang', [$from, $to]);
            });
        }
        $requests = $q->latest('id')->paginate(10);
        $pegawaiOptions = [];
        if (in_array($user->role, ['manager','admin'])) {
            $pegawaiOptions = User::orderBy('name')->get(['id','name','email']);
        }
        return view('sppd.index', [
            'requests' => $requests,
            'filters' => [
                'status' => $request->string('status')->toString(),
                'dari' => $from?->format('Y-m-d'),
                'sampai' => $to?->format('Y-m-d'),
                'pegawai_id' => $request->integer('pegawai_id'),
            ],
            'pegawaiOptions' => $pegawaiOptions,
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', SppdRequest::class);
        return view('sppd.create');
    }

    public function store(StoreSppdRequest $request): RedirectResponse
    {
        $this->authorize('create', SppdRequest::class);
        $data = $request->validated();
        if (empty($data['kode'])) {
            $data['kode'] = 'SPPD-'.now()->format('Ymd').'-'.str_pad((string) (SppdRequest::max('id') + 1), 3, '0', STR_PAD_LEFT);
        }
        $data['pegawai_id'] = Auth::id();
        $data['status'] = $data['status'] ?? 'draft';
        $sppd = SppdRequest::create($data);
        return redirect()->route('sppd.show', $sppd);
    }

    public function show(SppdRequest $sppd): View
    {
        $this->authorize('view', $sppd);
        $sppd->load(['expenses', 'attachments', 'approvals']);
        $officers = \App\Models\User::whereIn('role', ['admin','manager'])->orderBy('name')->get(['id','name','email','role']);
        return view('sppd.show', compact('sppd', 'officers'));
    }

    public function pdf(SppdRequest $sppd)
    {
        $this->authorize('view', $sppd);
        $sppd->load(['expenses', 'attachments', 'approvals', 'pegawai', 'pejabatPerintah']);
        $html = view('sppd.pdf', compact('sppd'))->render();
        $mpdf = new \Mpdf\Mpdf([
            'margin_top' => 20,
            'margin_bottom' => 20,
            'margin_left' => 15,
            'margin_right' => 15,
        ]);
        $mpdf->SetTitle('Laporan SPPD '.$sppd->kode);
        $mpdf->WriteHTML($html);
        $content = $mpdf->Output('', 'S');
        $filename = 'Laporan-SPPD-'.$sppd->kode.'.pdf';
        return response($content, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    public function edit(SppdRequest $sppd): View
    {
        $this->authorize('update', $sppd);
        return view('sppd.edit', compact('sppd'));
    }

    public function update(UpdateSppdRequest $request, SppdRequest $sppd): RedirectResponse
    {
        $this->authorize('update', $sppd);
        $sppd->update($request->validated());
        return redirect()->route('sppd.show', $sppd);
    }

    public function destroy(SppdRequest $sppd): RedirectResponse
    {
        $this->authorize('delete', $sppd);
        $sppd->delete();
        return redirect()->route('sppd.index');
    }

    public function ajukan(SppdRequest $sppd): RedirectResponse
    {
        $this->authorize('update', $sppd);
        $sppd->update(['status' => 'diajukan', 'alasan_penolakan' => null]);
        SppdApproval::create([
            'sppd_id' => $sppd->id,
            'approver_id' => null,
            'status' => 'diajukan',
            'catatan' => 'Pengajuan oleh pegawai',
            'acted_at' => now(),
        ]);
        return redirect()->route('sppd.show', $sppd);
    }

    public function setujui(SppdRequest $sppd): RedirectResponse
    {
        $this->authorize('approve', $sppd);
        $role = Auth::user()->role;
        if ($role === 'admin') {
            // Admin approve pertama
            SppdApproval::create([
                'sppd_id' => $sppd->id,
                'approver_id' => Auth::id(),
                'status' => 'disetujui',
                'catatan' => 'Disetujui Admin',
                'acted_at' => now(),
            ]);
            // Status tetap 'diajukan' sampai manager approve
        } elseif ($role === 'manager') {
            // Cek apakah sudah ada approval dari admin
            $adminApproval = $sppd->approvals()->whereHas('approver', function($q) {
                $q->where('role', 'admin');
            })->where('status', 'disetujui')->exists();
            
            if ($adminApproval) {
                $sppd->update([
                    'status' => 'disetujui',
                    'disetujui_oleh' => Auth::id(),
                    'disetujui_pada' => now(),
                    'alasan_penolakan' => null,
                ]);
                SppdApproval::create([
                    'sppd_id' => $sppd->id,
                    'approver_id' => Auth::id(),
                    'status' => 'disetujui',
                    'catatan' => 'Disetujui Manager',
                    'acted_at' => now(),
                ]);
                event(new SppdApproved($sppd));
            } else {
                return redirect()->back()->withErrors(['error' => 'SPPD harus disetujui admin terlebih dahulu.']);
            }
        }
        return redirect()->route('sppd.show', $sppd);
    }

    public function tolak(Request $request, SppdRequest $sppd): RedirectResponse
    {
        $this->authorize('approve', $sppd);
        $request->validate(['alasan_penolakan' => ['required','string']]);
        $sppd->update([
            'status' => 'ditolak',
            'alasan_penolakan' => $request->string('alasan_penolakan'),
            'disetujui_oleh' => Auth::id(),
            'disetujui_pada' => now(),
        ]);
        SppdApproval::create([
            'sppd_id' => $sppd->id,
            'approver_id' => Auth::id(),
            'status' => 'ditolak',
            'catatan' => $request->string('alasan_penolakan'),
            'acted_at' => now(),
        ]);
        event(new SppdRejected($sppd, $request->string('alasan_penolakan')));
        return redirect()->route('sppd.show', $sppd);
    }

    public function ajukanUlang(SppdRequest $sppd): RedirectResponse
    {
        $this->authorize('update', $sppd);
        $sppd->update([
            'status' => 'diajukan',
            'alasan_penolakan' => null,
        ]);
        SppdApproval::create([
            'sppd_id' => $sppd->id,
            'approver_id' => null,
            'status' => 'diajukan',
            'catatan' => 'Pengajuan ulang',
            'acted_at' => now(),
        ]);
        return redirect()->route('sppd.show', $sppd);
    }
}
