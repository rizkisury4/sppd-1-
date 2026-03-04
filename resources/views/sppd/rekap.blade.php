<x-dashboard-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Rekapitulasi SPPD</h2>
            <a href="{{ request()->fullUrlWithQuery(['export' => 'csv']) }}" class="inline-flex items-center justify-center px-4 py-2 rounded-md text-sm font-semibold shadow-sm ring-1 ring-inset bg-emerald-600 text-white hover:bg-emerald-700 ring-emerald-700/20 dark:bg-emerald-500 dark:hover:bg-emerald-400 dark:ring-white/10">Ekspor CSV</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="GET" action="{{ route('sppd.rekap') }}" class="mb-4 grid sm:grid-cols-5 gap-3 items-end">
                        @if(isset($pegawaiOptions) && count($pegawaiOptions))
                            <div class="sm:col-span-2">
                                <label class="block mb-1">Pegawai</label>
                                <select name="pegawai_id" class="w-full rounded border-gray-300 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100">
                                    <option value="">Semua</option>
                                    @foreach($pegawaiOptions as $p)
                                        <option value="{{ $p->id }}" @selected(($filters['pegawai_id'] ?? '')==$p->id)>{{ $p->name }} ({{ $p->email }})</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        <div>
                            <label class="block mb-1">Status</label>
                            <select name="status" class="w-full rounded border-gray-300 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100">
                                <option value="">Semua</option>
                                @foreach(['draft','diajukan','disetujui','ditolak','selesai'] as $st)
                                    <option value="{{ $st }}" @selected(($filters['status'] ?? '')===$st)>{{ ucfirst($st) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block mb-1">Dari</label>
                            <input type="date" name="dari" value="{{ $filters['dari'] ?? '' }}" class="w-full rounded border-gray-300 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100" />
                        </div>
                        <div>
                            <label class="block mb-1">Sampai</label>
                            <input type="date" name="sampai" value="{{ $filters['sampai'] ?? '' }}" class="w-full rounded border-gray-300 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100" />
                        </div>
                        <div class="flex gap-2">
                            <button class="inline-flex items-center justify-center px-4 py-2 rounded-md text-sm font-semibold shadow-sm ring-1 ring-inset bg-blue-600 text-white hover:bg-blue-700 ring-blue-700/20 dark:bg-blue-500 dark:hover:bg-blue-400 dark:ring-white/10">Terapkan</button>
                            <a href="{{ route('sppd.rekap') }}" class="inline-flex items-center justify-center px-4 py-2 rounded-md text-sm font-semibold shadow-sm ring-1 ring-inset bg-slate-100 text-slate-800 hover:bg-slate-200 ring-slate-300 dark:bg-slate-700 dark:text-slate-100 dark:hover:bg-slate-600 dark:ring-white/10">Reset</a>
                        </div>
                    </form>

                    <div class="grid sm:grid-cols-3 gap-4 mb-6">
                        <div class="p-4 rounded bg-slate-50 dark:bg-slate-700">
                            <div class="text-sm text-slate-500">Total Perjalanan</div>
                            <div class="text-2xl font-semibold">{{ $totalPerjalanan }}</div>
                        </div>
                    </div>

                    <h3 class="font-semibold mb-2">Total Biaya per Kategori</h3>
                    @if($perKategori->count())
                        <table class="min-w-full text-left text-sm">
                            <thead>
                                <tr>
                                    <th class="px-3 py-2">Kategori</th>
                                    <th class="px-3 py-2">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($perKategori as $row)
                                    <tr class="border-b border-gray-100 dark:border-gray-700">
                                        <td class="px-3 py-2">{{ ucfirst($row->kategori) }}</td>
                                        <td class="px-3 py-2">{{ number_format($row->total, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p>Tidak ada data biaya pada periode/penyaring ini.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-dashboard-layout>
