<x-dashboard-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Master Data</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white dark:bg-gray-800 shadow rounded-xl ring-1 ring-gray-100 dark:ring-gray-700 p-6">
                <h3 class="font-semibold text-lg mb-4 text-slate-800 dark:text-slate-100">Ringkasan</h3>
                <div class="grid sm:grid-cols-3 lg:grid-cols-4 gap-4">
                    <div class="p-4 rounded-lg ring-1 ring-gray-200 dark:ring-white/10">
                        <div class="text-xs uppercase text-slate-500">Total User</div>
                        <div class="mt-1 text-3xl font-bold text-slate-800 dark:text-slate-100">{{ $totalUsers }}</div>
                    </div>
                    <div class="p-4 rounded-lg ring-1 ring-gray-200 dark:ring-white/10">
                        <div class="text-xs uppercase text-slate-500">Total SPT</div>
                        <div class="mt-1 text-3xl font-bold text-slate-800 dark:text-slate-100">{{ $totalSPT }}</div>
                    </div>
                    <div class="p-4 rounded-lg ring-1 ring-gray-200 dark:ring-white/10">
                        <div class="text-xs uppercase text-slate-500">SPT Diterima</div>
                        <div class="mt-1 text-3xl font-bold text-emerald-700 dark:text-emerald-400">{{ $sptDiterima }}</div>
                    </div>
                    <div class="p-4 rounded-lg ring-1 ring-gray-200 dark:ring-white/10">
                        <div class="text-xs uppercase text-slate-500">SPT Ditolak</div>
                        <div class="mt-1 text-3xl font-bold text-rose-700 dark:text-rose-400">{{ $sptDitolak }}</div>
                    </div>
                    <div class="p-4 rounded-lg ring-1 ring-gray-200 dark:ring-white/10">
                        <div class="text-xs uppercase text-slate-500">Banyak Kota Tujuan</div>
                        <div class="mt-1 text-3xl font-bold text-slate-800 dark:text-slate-100">{{ $banyakKotaTujuan }}</div>
                    </div>
                    <div class="p-4 rounded-lg ring-1 ring-gray-200 dark:ring-white/10">
                        <div class="text-xs uppercase text-slate-500">Login Terakhir</div>
                        <div class="mt-1 text-base font-semibold text-slate-800 dark:text-slate-100">{{ $lastLogin ?? '-' }}</div>
                    </div>
                    <div class="p-4 rounded-lg ring-1 ring-gray-200 dark:ring-white/10">
                        <div class="text-xs uppercase text-slate-500">Anggaran Murni</div>
                        <div class="mt-1 text-2xl font-bold text-slate-800 dark:text-slate-100">Rp {{ number_format($anggaranMurni, 0, ',', '.') }}</div>
                    </div>
                    <div class="p-4 rounded-lg ring-1 ring-gray-200 dark:ring-white/10">
                        <div class="text-xs uppercase text-slate-500">Realisasi Anggaran</div>
                        <div class="mt-1 text-2xl font-bold text-amber-700 dark:text-amber-400">Rp {{ number_format($realisasi, 0, ',', '.') }}</div>
                    </div>
                    <div class="p-4 rounded-lg ring-1 ring-gray-200 dark:ring-white/10">
                        <div class="text-xs uppercase text-slate-500">Sisa Anggaran</div>
                        <div class="mt-1 text-2xl font-bold text-emerald-700 dark:text-emerald-400">Rp {{ number_format($sisaAnggaran, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow rounded-xl ring-1 ring-gray-100 dark:ring-gray-700 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-lg text-slate-800 dark:text-slate-100">History Online</h3>
                    <span class="text-xs text-slate-500">14 hari terakhir</span>
                </div>
                <div>
                    <canvas id="onlineChart" height="110"></canvas>
                    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
                    <script>
                        const labels = <?php echo json_encode($labels); ?>;
                        const data = <?php echo json_encode($series); ?>;
                        const ctx = document.getElementById('onlineChart').getContext('2d');
                        if (labels && labels.length) {
                            new Chart(ctx, {
                                type: 'line',
                                data: {
                                    labels,
                                    datasets: [{
                                        label: 'Aktivitas Online (sessions)',
                                        data,
                                        borderColor: '#3b82f6',
                                        backgroundColor: 'rgba(59,130,246,0.15)',
                                        tension: 0.3,
                                        fill: true,
                                        pointRadius: 3
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    scales: {
                                        y: { beginAtZero: true, ticks: { precision: 0 } }
                                    },
                                    plugins: {
                                        legend: { display: false }
                                    }
                                }
                            });
                        } else {
                            ctx.font = '14px sans-serif';
                            ctx.fillStyle = '#64748b';
                            ctx.fillText('Belum ada data aktivitas.', 10, 30);
                        }
                    </script>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow rounded-xl ring-1 ring-gray-100 dark:ring-gray-700 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-lg text-slate-800 dark:text-slate-100">Master Data User — Data SPT</h3>
                    <span class="text-xs text-slate-500">Top 50 terakhir</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm">
                        <thead class="border-b border-gray-200 dark:border-gray-700">
                            <tr>
                                <th class="px-3 py-2">Kode</th>
                                <th class="px-3 py-2">Pegawai</th>
                                <th class="px-3 py-2">Tujuan</th>
                                <th class="px-3 py-2">Kota</th>
                                <th class="px-3 py-2">Berangkat</th>
                                <th class="px-3 py-2">Pulang</th>
                                <th class="px-3 py-2">Jenis</th>
                                <th class="px-3 py-2">Status</th>
                                <th class="px-3 py-2 text-right">Realisasi (Rp)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sppds as $r)
                                <tr class="border-b border-gray-100 dark:border-gray-700">
                                    <td class="px-3 py-2">{{ $r->kode }}</td>
                                    <td class="px-3 py-2">{{ $r->pegawai?->name ?? '-' }}</td>
                                    <td class="px-3 py-2">{{ $r->tujuan }}</td>
                                    <td class="px-3 py-2">{{ $r->kota ?? '-' }}</td>
                                    <td class="px-3 py-2">{{ $r->tanggal_berangkat?->format('Y-m-d') }}</td>
                                    <td class="px-3 py-2">{{ $r->tanggal_pulang?->format('Y-m-d') }}</td>
                                    <td class="px-3 py-2">
                                        {{ $r->jenis_surat === 'undangan' ? 'Undangan' : 'Surat Tugas' }}
                                    </td>
                                    <td class="px-3 py-2">{{ ucfirst($r->status) }}</td>
                                    <td class="px-3 py-2 text-right">{{ number_format((float) ($r->expenses_sum_jumlah ?? 0), 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-3 py-4 text-slate-500">Belum ada data.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow rounded-xl ring-1 ring-gray-100 dark:ring-gray-700 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-lg text-slate-800 dark:text-slate-100">Pengguna Dashboard</h3>
                    <span class="text-xs text-slate-500">Semua user</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm">
                        <thead class="border-b border-gray-200 dark:border-gray-700">
                            <tr>
                                <th class="px-3 py-2">Nama</th>
                                <th class="px-3 py-2">Jabatan</th>
                                <th class="px-3 py-2">Email</th>
                                <th class="px-3 py-2">Divisi</th>
                                <th class="px-3 py-2">Awal Bergabung</th>
                                <th class="px-3 py-2">Is Active</th>
                                <th class="px-3 py-2">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($userRows as $u)
                                <tr class="border-b border-gray-100 dark:border-gray-700">
                                    <td class="px-3 py-2">{{ $u['name'] }}</td>
                                    <td class="px-3 py-2 capitalize">{{ $u['role'] }}</td>
                                    <td class="px-3 py-2">{{ $u['email'] }}</td>
                                    <td class="px-3 py-2">{{ $u['department'] }}</td>
                                    <td class="px-3 py-2">{{ $u['joined'] }}</td>
                                    <td class="px-3 py-2">
                                        <span class="inline-block w-2 h-2 rounded-full {{ $u['active'] ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                        <span class="ml-1 text-xs text-slate-600 dark:text-slate-400">{{ $u['active'] ? 'Online' : 'Offline' }}</span>
                                    </td>
                                    <td class="px-3 py-2">
                                        <a href="{{ route('admin.users.edit', $u['id']) }}" class="text-blue-600 dark:text-blue-400 hover:underline">Edit</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-3 py-4 text-slate-500">Belum ada pengguna.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-dashboard-layout>
