<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sppd\SppdExpense;
use App\Models\Sppd\SppdRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class MetricsController extends Controller
{
    public function index(Request $request): View
    {
        abort_if($request->user()->role !== 'admin', 403);

        $totalUsers = User::count();
        $totalSPT = SppdRequest::count();
        $sptDiterima = SppdRequest::where('status', 'disetujui')->count();
        $sptDitolak = SppdRequest::where('status', 'ditolak')->count();
        $banyakKotaTujuan = SppdRequest::whereNotNull('kota')->distinct('kota')->count('kota');

        $anggaranMurni = 500_000_000;
        $realisasi = (float) SppdExpense::sum('jumlah');
        $sisaAnggaran = max(0, $anggaranMurni - $realisasi);

        $lastLogin = null;
        $series = [];
        $labels = [];

        if (Schema::hasTable('sessions')) {
            $sessions = DB::table('sessions')->select(['user_id', 'last_activity'])->whereNotNull('user_id')->get();
            if ($sessions->count()) {
                $lastTs = $sessions->max('last_activity');
                $lastLogin = $lastTs ? date('Y-m-d H:i:s', $lastTs) : null;
                $days = [];
                for ($i = 13; $i >= 0; $i--) {
                    $d = date('Y-m-d', strtotime("-{$i} days"));
                    $labels[] = $d;
                    $days[$d] = 0;
                }
                foreach ($sessions as $s) {
                    $d = date('Y-m-d', (int) $s->last_activity);
                    if (isset($days[$d])) {
                        $days[$d] += 1;
                    }
                }
                $series = array_values($days);
            }
        }

        $sppds = SppdRequest::with(['pegawai'])
            ->withSum('expenses', 'jumlah')
            ->latest('id')
            ->limit(50)
            ->get([
                'id',
                'kode',
                'pegawai_id',
                'tujuan',
                'kota',
                'tanggal_berangkat',
                'tanggal_pulang',
                'jenis_perjalanan',
                'jenis_surat',
                'status',
            ]);

        $activeMap = [];
        if (Schema::hasTable('sessions')) {
            $threshold = time() - 15 * 60;
            $actives = DB::table('sessions')
                ->select('user_id', DB::raw('max(last_activity) as la'))
                ->whereNotNull('user_id')
                ->groupBy('user_id')
                ->get();
            foreach ($actives as $a) {
                $activeMap[$a->user_id] = ((int) $a->la) >= $threshold;
            }
        }
        $users = User::orderBy('name')->get(['id','name','email','role','created_at']);
        $userRows = $users->map(function (User $u) {
            $lastDept = SppdRequest::where('pegawai_id', $u->id)
                ->whereNotNull('department_id')
                ->latest('id')
                ->value('department_id');
            $deptName = null;
            if ($lastDept) {
                $deptName = DB::table('departments')->where('id', $lastDept)->value('name');
            }
            return [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'role' => $u->role,
                'department' => $deptName ?? '-',
                'joined' => optional($u->created_at)->format('Y-m-d'),
            ];
        })->all();
        foreach ($userRows as &$row) {
            $row['active'] = $activeMap[$row['id']] ?? false;
        }

        return view('admin.metrics', compact(
            'totalUsers',
            'totalSPT',
            'sptDiterima',
            'sptDitolak',
            'banyakKotaTujuan',
            'anggaranMurni',
            'realisasi',
            'sisaAnggaran',
            'lastLogin',
            'labels',
            'series',
            'sppds',
            'userRows'
        ));
    }
}
