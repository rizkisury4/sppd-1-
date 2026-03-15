<x-dashboard-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('SPPD') }}
            </h2>
            <a href="{{ route('sppd.create') }}" class="inline-flex items-center justify-center px-4 py-2 rounded-md text-sm font-semibold shadow-sm ring-1 ring-inset bg-blue-600 text-white hover:bg-blue-700 ring-blue-700/20 dark:bg-blue-500 dark:hover:bg-blue-400 dark:ring-white/10 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">Buat SPPD</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="GET" action="{{ route('sppd.index') }}" class="mb-4 grid sm:grid-cols-5 gap-3 items-end">
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
                            <button class="inline-flex items-center justify-center px-4 py-2 rounded-md text-sm font-semibold shadow-sm ring-1 ring-inset bg-blue-600 text-white hover:bg-blue-700 ring-blue-700/20 dark:bg-blue-500 dark:hover:bg-blue-400 dark:ring-white/10">Filter</button>
                            <a href="{{ route('sppd.index') }}" class="inline-flex items-center justify-center px-4 py-2 rounded-md text-sm font-semibold shadow-sm ring-1 ring-inset bg-slate-100 text-slate-800 hover:bg-slate-200 ring-slate-300 dark:bg-slate-700 dark:text-slate-100 dark:hover:bg-slate-600 dark:ring-white/10">Reset</a>
                        </div>
                    </form>
                    @if(isset($requests) && $requests->count())
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-left text-sm">
                                <thead class="border-b border-gray-200 dark:border-gray-700">
                                    <tr>
                                        <th class="px-3 py-2">Kode</th>
                                        <th class="px-3 py-2">Tujuan</th>
                                        <th class="px-3 py-2">Tgl Berangkat</th>
                                        <th class="px-3 py-2">Tgl Pulang</th>
                                        <th class="px-3 py-2">Status</th>
                                        <th class="px-3 py-2">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($requests as $r)
                                        <tr class="border-b border-gray-100 dark:border-gray-700">
                                            <td class="px-3 py-2">{{ $r->kode }}</td>
                                            <td class="px-3 py-2">{{ $r->tujuan }}</td>
                                            <td class="px-3 py-2">{{ $r->tanggal_berangkat->format('Y-m-d') }}</td>
                                            <td class="px-3 py-2">{{ $r->tanggal_pulang->format('Y-m-d') }}</td>
                                            <td class="px-3 py-2">{{ $r->status }}</td>
                                            <td class="px-3 py-2">
                                                <a class="text-blue-600 dark:text-blue-400 hover:underline font-medium" href="{{ route('sppd.show', $r) }}">Detail</a>
                                                @if(in_array(auth()->user()->role, ['admin','manager']))
                                                    <span class="mx-1">|</span>
                                                    <a class="text-emerald-700 dark:text-emerald-400 hover:underline font-medium" href="{{ route('sppd.pdf', $r) }}">PDF</a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">{{ $requests->links() }}</div>
                    @else
                        <p>Belum ada pengajuan SPPD.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-dashboard-layout>
