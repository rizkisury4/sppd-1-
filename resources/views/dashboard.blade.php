<x-dashboard-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>
    @if(isset($pegawaiStats))
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
                            <div class="p-4 bg-white dark:bg-gray-800 rounded-xl shadow ring-1 ring-gray-100 dark:ring-gray-700">
                                <div class="text-xs uppercase text-gray-500 dark:text-gray-400">Draft</div>
                                <div class="mt-1 text-3xl font-bold text-gray-800 dark:text-gray-100">{{ $pegawaiStats['draft'] }}</div>
                            </div>
                            <div class="p-4 bg-white dark:bg-gray-800 rounded-xl shadow ring-1 ring-gray-100 dark:ring-gray-700">
                                <div class="text-xs uppercase text-gray-500 dark:text-gray-400">Diajukan</div>
                                <div class="mt-1 text-3xl font-bold text-gray-800 dark:text-gray-100">{{ $pegawaiStats['diajukan'] }}</div>
                            </div>
                            <div class="p-4 bg-white dark:bg-gray-800 rounded-xl shadow ring-1 ring-gray-100 dark:ring-gray-700">
                                <div class="text-xs uppercase text-gray-500 dark:text-gray-400">Disetujui</div>
                                <div class="mt-1 text-3xl font-bold text-gray-800 dark:text-gray-100">{{ $pegawaiStats['disetujui'] }}</div>
                            </div>
                            <div class="p-4 bg-white dark:bg-gray-800 rounded-xl shadow ring-1 ring-gray-100 dark:ring-gray-700">
                                <div class="text-xs uppercase text-gray-500 dark:text-gray-400">Ditolak</div>
                                <div class="mt-1 text-3xl font-bold text-gray-800 dark:text-gray-100">{{ $pegawaiStats['ditolak'] }}</div>
                            </div>
                            <div class="p-4 bg-white dark:bg-gray-800 rounded-xl shadow ring-1 ring-gray-100 dark:ring-gray-700">
                                <div class="text-xs uppercase text-gray-500 dark:text-gray-400">Selesai</div>
                                <div class="mt-1 text-3xl font-bold text-gray-800 dark:text-gray-100">{{ $pegawaiStats['selesai'] }}</div>
                            </div>
                        </div>
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-xl ring-1 ring-gray-100 dark:ring-gray-700">
                            <div class="p-6 text-gray-900 dark:text-gray-100">
                                <h3 class="font-semibold mb-3">Ringkasan SPPD Saya</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Lihat dan kelola pengajuan SPPD Anda.</p>
                            </div>
                        </div>
    @endif

    @if(isset($managerPending))
        <div class="mt-6 bg-white dark:bg-gray-800 overflow-hidden shadow rounded-xl ring-1 ring-gray-100 dark:ring-gray-700">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <h3 class="font-semibold mb-3">Antrian Persetujuan</h3>
                @if($managerPending->count())
                    <ul class="list-disc ml-4">
                        @foreach($managerPending as $r)
                            <li><a class="text-blue-600 dark:text-blue-400 hover:underline" href="{{ route('sppd.show', $r) }}">{{ $r->kode }} - {{ $r->tujuan }} ({{ $r->status }})</a></li>
                        @endforeach
                    </ul>
                @else
                    <p>Tidak ada pengajuan menunggu.</p>
                @endif
            </div>
        </div>
    @endif

    <div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-xl ring-1 ring-gray-100 dark:ring-gray-700">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="font-semibold mb-3">Recent Activity</h3>
                    @if(isset($recent) && $recent->count())
                        <ul class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($recent as $item)
                                <li class="py-3 flex items-center justify-between">
                                    <div>
                                        <div class="font-medium">{{ $item->kode }} • {{ $item->tujuan }}</div>
                                        <div class="text-xs text-gray-500">{{ ucfirst($item->status) }} • {{ $item->updated_at->diffForHumans() }}</div>
                                    </div>
                                    <a href="{{ route('sppd.show', $item) }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">Lihat</a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-sm text-gray-600 dark:text-gray-400">Belum ada aktivitas terbaru.</p>
                    @endif
                </div>
            </div>
        </div>
        <div>
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-xl ring-1 ring-gray-100 dark:ring-gray-700">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="font-semibold">Total Pencairan</h3>
                        <div class="text-sm font-semibold">{{ number_format($disbursementTotal, 2) }}</div>
                    </div>
                    <canvas id="disbursementDonut" height="180"></canvas>
                    <div class="mt-4 grid grid-cols-2 gap-2">
                        @if(isset($disbursement) && $disbursement->count())
                            @foreach($disbursement as $row)
                                <div class="text-xs flex items-center justify-between">
                                    <span>{{ ucfirst($row->kategori) }}</span>
                                    <span>{{ number_format($row->total, 2) }}</span>
                                </div>
                            @endforeach
                        @else
                            <p class="text-sm text-gray-600 dark:text-gray-400 col-span-2">Belum ada data pencairan.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
        <script>
            window.addEventListener('DOMContentLoaded', function () {
                const el = document.getElementById('disbursementDonut');
                if (!el) return;
                const labels = {!! isset($disbursement) ? $disbursement->pluck('kategori')->map(fn($v)=>ucfirst($v))->toJson() : '[]' !!};
                const data = {!! isset($disbursement) ? $disbursement->pluck('total')->toJson() : '[]' !!};
                if (!labels.length) return;
                const colors = ['#6366F1','#22C55E','#F59E0B','#EF4444','#06B6D4','#84CC16','#A855F7','#F97316'];
                new Chart(el, { type: 'doughnut', data: { labels, datasets: [{ data, backgroundColor: labels.map((_, i) => colors[i % colors.length]), borderWidth: 0 }] }, options: { responsive: true, cutout: '60%', plugins: { legend: { display: false } } } });
            });
        </script>
    @endpush
</x-dashboard-layout>
