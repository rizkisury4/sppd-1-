<?php

namespace App\Http\Controllers\Sppd;

use App\Http\Controllers\Controller;
use App\Models\Sppd\SppdExpense;
use App\Models\Sppd\SppdRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View|StreamedResponse
    {
        $user = Auth::user();
        $q = SppdRequest::query();

        if ($user->role === 'pegawai') {
            $q->where('pegawai_id', $user->id);
        } elseif ($request->filled('pegawai_id')) {
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

        $ids = $q->clone()->pluck('id');
        $totalPerjalanan = $ids->count();

        $perKategori = SppdExpense::select('kategori', DB::raw('SUM(jumlah) as total'))
            ->whereIn('sppd_id', $ids)
            ->groupBy('kategori')
            ->orderBy('kategori')
            ->get();

        if ($request->string('export')->toString() === 'csv') {
            $filename = 'rekap_sppd_'.Str::slug(now()->toDateTimeString()).'.csv';
            return response()->streamDownload(function () use ($perKategori, $totalPerjalanan) {
                $out = fopen('php://output', 'w');
                fputcsv($out, ['Kategori', 'Total']);
                foreach ($perKategori as $row) {
                    fputcsv($out, [$row->kategori, number_format($row->total, 2, '.', '')]);
                }
                fputcsv($out, []);
                fputcsv($out, ['Total Perjalanan', $totalPerjalanan]);
                fclose($out);
            }, $filename, ['Content-Type' => 'text/csv']);
        }

        $pegawaiOptions = [];
        if (in_array($user->role, ['manager','admin'])) {
            $pegawaiOptions = User::orderBy('name')->get(['id','name','email']);
        }

        return view('sppd.rekap', [
            'filters' => [
                'status' => $request->string('status')->toString(),
                'dari' => $from?->format('Y-m-d'),
                'sampai' => $to?->format('Y-m-d'),
                'pegawai_id' => $request->integer('pegawai_id'),
            ],
            'totalPerjalanan' => $totalPerjalanan,
            'perKategori' => $perKategori,
            'pegawaiOptions' => $pegawaiOptions,
        ]);
    }
}

