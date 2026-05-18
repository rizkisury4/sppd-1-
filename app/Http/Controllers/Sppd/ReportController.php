<?php

namespace App\Http\Controllers\Sppd;

use App\Http\Controllers\Controller;
use App\Models\Sppd\SppdExpense;
use App\Models\Sppd\SppdRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View|StreamedResponse
    {
        $user = $request->user();
        $q = SppdRequest::query()
            ->with('pegawai.employee')
            ->withSum('expenses as total_biaya', 'jumlah');

        if ($user->role === 'pegawai') {
            $q->where('pegawai_id', $user->id);
        } elseif ($request->filled('pegawai_id')) {
            $q->where('pegawai_id', $request->integer('pegawai_id'));
        }

        $filters = $this->applyReportFilters($q, $request);
        $requests = (clone $q)->latest('tanggal_berangkat')->latest('id')->get();
        $ids = $requests->pluck('id');
        $totalPerjalanan = $requests->count();

        $perKategori = SppdExpense::select('kategori', DB::raw('SUM(jumlah) as total'))
            ->whereIn('sppd_id', $ids)
            ->groupBy('kategori')
            ->orderBy('kategori')
            ->get();

        $export = $request->string('export')->toString();
        if (in_array($export, ['csv', 'excel'], true)) {
            return $this->exportReport($export, $requests, $perKategori, $totalPerjalanan);
        }

        $pegawaiOptions = [];
        if (in_array($user->role, ['manager','admin','direksi'], true)) {
            $pegawaiOptions = User::with('employee')
                ->where('role', 'pegawai')
                ->whereNotNull('employee_id')
                ->orderBy('name')
                ->get(['id','name','email','employee_id'])
                ->map(fn (User $pegawai) => [
                    'id' => $pegawai->id,
                    'label' => $pegawai->employee
                        ? sprintf('%s (%s)', $pegawai->employee->name, $pegawai->employee->nip)
                        : sprintf('%s (%s)', $pegawai->name, $pegawai->email),
                ]);
        }

        return view('sppd.rekap', [
            'filters' => $filters,
            'totalPerjalanan' => $totalPerjalanan,
            'perKategori' => $perKategori,
            'pegawaiOptions' => $pegawaiOptions,
            'requests' => $requests,
        ]);
    }

    protected function applyReportFilters(Builder $query, Request $request): array
    {
        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->toString());
        }

        $search = trim($request->string('q')->toString());
        if ($search !== '') {
            $like = '%'.$search.'%';
            $query->where(function (Builder $builder) use ($like) {
                $builder->where('kode', 'like', $like)
                    ->orWhere('tujuan', 'like', $like)
                    ->orWhere('kota', 'like', $like)
                    ->orWhereHas('pegawai', function (Builder $pegawaiQuery) use ($like) {
                        $pegawaiQuery->where('name', 'like', $like)
                            ->orWhere('email', 'like', $like)
                            ->orWhereHas('employee', function (Builder $employeeQuery) use ($like) {
                                $employeeQuery->where('name', 'like', $like)
                                    ->orWhere('nip', 'like', $like);
                            });
                    });
            });
        }

        $from = $request->date('dari');
        $to = $request->date('sampai');
        $this->applyDateRangeFilter($query, $from?->toDateString(), $to?->toDateString());

        return [
            'q' => $search,
            'status' => $request->string('status')->toString(),
            'dari' => $from?->format('Y-m-d'),
            'sampai' => $to?->format('Y-m-d'),
            'pegawai_id' => $request->integer('pegawai_id'),
        ];
    }

    protected function applyDateRangeFilter(Builder $query, ?string $from, ?string $to): void
    {
        if ($from && $to) {
            $query->whereDate('tanggal_berangkat', '<=', $to)
                ->whereDate('tanggal_pulang', '>=', $from);

            return;
        }

        if ($from) {
            $query->whereDate('tanggal_pulang', '>=', $from);
        }

        if ($to) {
            $query->whereDate('tanggal_berangkat', '<=', $to);
        }
    }

    protected function exportReport(string $format, $requests, $perKategori, int $totalPerjalanan): StreamedResponse
    {
        $timestamp = Str::slug(now()->format('Y-m-d-H-i-s'));

        if ($format === 'csv') {
            return response()->streamDownload(function () use ($requests, $perKategori, $totalPerjalanan) {
                $out = fopen('php://output', 'w');
                fwrite($out, "\xEF\xBB\xBF");

                fputcsv($out, ['Ringkasan Rekap SPPD']);
                fputcsv($out, ['Total Perjalanan', $totalPerjalanan]);
                fputcsv($out, []);
                fputcsv($out, ['Kategori', 'Total']);
                foreach ($perKategori as $row) {
                    fputcsv($out, [$row->kategori, number_format((float) $row->total, 2, '.', '')]);
                }

                fputcsv($out, []);
                fputcsv($out, ['Detail SPPD']);
                fputcsv($out, ['Kode', 'Pegawai', 'Tujuan', 'Kota', 'Tanggal Berangkat', 'Tanggal Pulang', 'Lama Hari', 'Status', 'Total Biaya']);
                foreach ($requests as $request) {
                    fputcsv($out, [
                        $request->kode,
                        $this->pegawaiLabel($request),
                        $request->tujuan,
                        $request->kota,
                        optional($request->tanggal_berangkat)->format('Y-m-d'),
                        optional($request->tanggal_pulang)->format('Y-m-d'),
                        $request->lama_hari,
                        $request->status,
                        number_format((float) ($request->total_biaya ?? 0), 2, '.', ''),
                    ]);
                }

                fclose($out);
            }, 'rekap_sppd_'.$timestamp.'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
        }

        return response()->streamDownload(function () use ($requests, $perKategori, $totalPerjalanan) {
            echo '<html><head><meta charset="UTF-8"></head><body>';
            echo '<table border="1">';
            echo '<tr><th colspan="2">Ringkasan Rekap SPPD</th></tr>';
            echo '<tr><td>Total Perjalanan</td><td>'.e((string) $totalPerjalanan).'</td></tr>';
            echo '</table><br>';
            echo '<table border="1">';
            echo '<tr><th>Kategori</th><th>Total</th></tr>';
            foreach ($perKategori as $row) {
                echo '<tr><td>'.e((string) $row->kategori).'</td><td>'.e(number_format((float) $row->total, 2, '.', '')).'</td></tr>';
            }
            echo '</table><br>';
            echo '<table border="1">';
            echo '<tr><th>Kode</th><th>Pegawai</th><th>Tujuan</th><th>Kota</th><th>Tanggal Berangkat</th><th>Tanggal Pulang</th><th>Lama Hari</th><th>Status</th><th>Total Biaya</th></tr>';
            foreach ($requests as $request) {
                echo '<tr>';
                echo '<td>'.e((string) $request->kode).'</td>';
                echo '<td>'.e($this->pegawaiLabel($request)).'</td>';
                echo '<td>'.e((string) $request->tujuan).'</td>';
                echo '<td>'.e((string) $request->kota).'</td>';
                echo '<td>'.e(optional($request->tanggal_berangkat)->format('Y-m-d') ?? '').'</td>';
                echo '<td>'.e(optional($request->tanggal_pulang)->format('Y-m-d') ?? '').'</td>';
                echo '<td>'.e((string) $request->lama_hari).'</td>';
                echo '<td>'.e((string) $request->status).'</td>';
                echo '<td>'.e(number_format((float) ($request->total_biaya ?? 0), 2, '.', '')).'</td>';
                echo '</tr>';
            }
            echo '</table>';
            echo '</body></html>';
        }, 'rekap_sppd_'.$timestamp.'.xls', ['Content-Type' => 'application/vnd.ms-excel; charset=UTF-8']);
    }

    protected function pegawaiLabel(SppdRequest $request): string
    {
        if ($request->pegawai?->employee) {
            return sprintf('%s (%s)', $request->pegawai->employee->name, $request->pegawai->employee->nip);
        }

        return $request->pegawai?->name ?? '-';
    }
}

