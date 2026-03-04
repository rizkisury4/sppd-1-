<?php

namespace App\Http\Controllers;

use App\Models\Sppd\SppdRequest;
use App\Models\Sppd\SppdExpense;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $user = Auth::user();

        $pegawaiStats = null;
        $managerPending = null;
        $adminStats = null;
        $recent = null;
        $disbursement = null;
        $disbursementTotal = 0;

        if ($user->role === 'pegawai') {
            $pegawaiStats = [
                'draft' => SppdRequest::where('pegawai_id', $user->id)->where('status', 'draft')->count(),
                'diajukan' => SppdRequest::where('pegawai_id', $user->id)->where('status', 'diajukan')->count(),
                'disetujui' => SppdRequest::where('pegawai_id', $user->id)->where('status', 'disetujui')->count(),
                'ditolak' => SppdRequest::where('pegawai_id', $user->id)->where('status', 'ditolak')->count(),
                'selesai' => SppdRequest::where('pegawai_id', $user->id)->where('status', 'selesai')->count(),
            ];
        }

        if (in_array($user->role, ['manager', 'admin'])) {
            $managerPending = SppdRequest::where('status', 'diajukan')->latest('id')->take(10)->get();
        }

        if ($user->role === 'admin') {
            $adminStats = [
                'total' => SppdRequest::count(),
                'diajukan' => SppdRequest::where('status', 'diajukan')->count(),
                'disetujui' => SppdRequest::where('status', 'disetujui')->count(),
                'ditolak' => SppdRequest::where('status', 'ditolak')->count(),
            ];
        }

        $recent = SppdRequest::select(['id','kode','tujuan','status','updated_at'])->latest('updated_at')->take(8)->get();
        $disbursement = SppdExpense::selectRaw("COALESCE(kategori, 'Lainnya') as kategori, SUM(jumlah) as total")
            ->whereHas('sppd', function ($q) {
                $q->whereIn('status', ['disetujui','selesai']);
            })
            ->groupBy('kategori')
            ->orderByDesc('total')
            ->get();
        $disbursementTotal = (float) $disbursement->sum('total');

        return view('dashboard', compact('pegawaiStats', 'managerPending', 'adminStats', 'recent', 'disbursement', 'disbursementTotal'));
    }
}
