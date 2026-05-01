<x-dashboard-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-semibold text-xl text-slate-900 dark:text-slate-100 leading-tight">Data Kepegawaian</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Master data pegawai untuk kebutuhan administrasi dan referensi SPPD.</p>
            </div>
            <a href="{{ route('admin.employees.create') }}" class="inline-flex items-center justify-center px-4 py-2 rounded-md text-sm font-semibold shadow-sm ring-1 ring-inset bg-blue-600 text-white hover:bg-blue-700 ring-blue-700/20 dark:bg-blue-500 dark:hover:bg-blue-400 dark:ring-white/10">Tambah Pegawai</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('status'))
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-900/60 dark:bg-emerald-950/30 dark:text-emerald-300">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white/95 dark:bg-slate-800/95 shadow-lg rounded-2xl ring-1 ring-slate-200/70 dark:ring-slate-700 p-6">
                <form method="GET" action="{{ route('admin.employees.index') }}" class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between mb-5">
                    <div class="w-full md:max-w-md">
                        <input type="text" name="q" value="{{ $keyword }}" placeholder="Cari nama, NIP, jabatan, atau status" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-slate-900 placeholder:text-slate-400 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100 dark:placeholder:text-slate-500" />
                    </div>
                    <div class="flex gap-2">
                        <button class="px-4 py-2.5 rounded-xl bg-slate-900 text-white text-sm font-semibold shadow-sm hover:bg-slate-700 dark:bg-slate-100 dark:text-slate-900 dark:hover:bg-white">Cari</button>
                        @if($keyword !== '')
                            <a href="{{ route('admin.employees.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-300 bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800">Reset</a>
                        @endif
                    </div>
                </form>

                <div class="overflow-x-auto rounded-xl border border-slate-200/80 dark:border-slate-700">
                    <table class="min-w-full text-left text-sm text-slate-700 dark:text-slate-100">
                        <thead class="bg-slate-100/80 text-slate-600 dark:bg-slate-900/60 dark:text-slate-300">
                            <tr>
                                <th class="px-4 py-3 font-semibold">NIP</th>
                                <th class="px-4 py-3 font-semibold">Nama</th>
                                <th class="px-4 py-3 font-semibold">Jabatan</th>
                                <th class="px-4 py-3 font-semibold">Status Pegawai</th>
                                <th class="px-4 py-3 font-semibold">Aktif</th>
                                <th class="px-4 py-3 font-semibold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200/80 dark:divide-slate-700">
                            @forelse($employees as $employee)
                                <tr class="align-top hover:bg-slate-50/80 dark:hover:bg-slate-700/40">
                                    <td class="px-4 py-3 whitespace-nowrap text-slate-900 dark:text-slate-100">{{ $employee->nip }}</td>
                                    <td class="px-4 py-3 text-slate-900 dark:text-slate-100">{{ $employee->name }}</td>
                                    <td class="px-4 py-3 text-slate-700 dark:text-slate-300">{{ $employee->position }}</td>
                                    <td class="px-4 py-3 text-slate-700 dark:text-slate-300">{{ $employee->employment_status }}</td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $employee->active ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300' : 'bg-slate-100 text-slate-600 dark:bg-slate-900 dark:text-slate-300' }}">
                                            {{ $employee->active ? 'Ya' : 'Tidak' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <a href="{{ route('admin.employees.edit', $employee) }}" class="text-blue-600 dark:text-blue-400 hover:underline">Edit</a>
                                        <form method="POST" action="{{ route('admin.employees.destroy', $employee) }}" class="inline-block ml-3" onsubmit="return confirm('Yakin hapus data pegawai ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-rose-600 dark:text-rose-400 hover:underline">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-6 text-slate-500 dark:text-slate-400">Belum ada data pegawai.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-5 text-slate-700 dark:text-slate-300">{{ $employees->links() }}</div>
            </div>
        </div>
    </div>
</x-dashboard-layout>