<x-dashboard-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Detail SPPD</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900 dark:text-gray-100 space-y-6">
                <div>
                    <p><span class="font-medium">Kode:</span> {{ $sppd->kode }}</p>
                    <p><span class="font-medium">Tujuan:</span> {{ $sppd->tujuan }}</p>
                    <p><span class="font-medium">Status:</span> {{ $sppd->status }}</p>
                </div>

                <div class="flex gap-3 flex-wrap">
                    @if(auth()->user()->id === $sppd->pegawai_id && in_array($sppd->status, ['draft','ditolak']))
                        <form method="POST" action="{{ route('sppd.ajukan', $sppd) }}">
                            @csrf
                            <button class="inline-flex items-center justify-center px-3 py-2 rounded-md text-sm font-semibold shadow-sm ring-1 ring-inset bg-blue-600 text-white hover:bg-blue-700 ring-blue-700/20 dark:bg-blue-500 dark:hover:bg-blue-400 dark:ring-white/10 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">Ajukan</button>
                        </form>
                        @if($sppd->status === 'ditolak')
                            <form method="POST" action="{{ route('sppd.ajukanUlang', $sppd) }}">
                                @csrf
                                <button class="inline-flex items-center justify-center px-3 py-2 rounded-md text-sm font-semibold shadow-sm ring-1 ring-inset bg-indigo-600 text-white hover:bg-indigo-700 ring-indigo-700/20 dark:bg-indigo-500 dark:hover:bg-indigo-400 dark:ring-white/10 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Ajukan Ulang</button>
                            </form>
                        @endif
                    @endif

                    @if(in_array(auth()->user()->role, ['admin','manager']) && $sppd->status === 'diajukan')
                        <form method="POST" action="{{ route('sppd.setujui', $sppd) }}">
                            @csrf
                            <button class="inline-flex items-center justify-center px-3 py-2 rounded-md text-sm font-semibold shadow-sm ring-1 ring-inset bg-emerald-600 text-white hover:bg-emerald-700 ring-emerald-700/20 dark:bg-emerald-500 dark:hover:bg-emerald-400 dark:ring-white/10 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-600">Setujui</button>
                        </form>
                        <form method="POST" action="{{ route('sppd.tolak', $sppd) }}" class="flex items-center gap-2">
                            @csrf
                            <input type="text" name="alasan_penolakan" class="rounded border-gray-300 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100" placeholder="Alasan penolakan" required />
                            <button class="inline-flex items-center justify-center px-3 py-2 rounded-md text-sm font-semibold shadow-sm ring-1 ring-inset bg-rose-600 text-white hover:bg-rose-700 ring-rose-700/20 dark:bg-rose-500 dark:hover:bg-rose-400 dark:ring-white/10 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-rose-600">Tolak</button>
                        </form>
                    @endif
                </div>

                <div>
                    <h3 class="font-semibold mb-2">Tambah Biaya</h3>
                    <form method="POST" action="{{ route('sppd.expenses.store', $sppd) }}" class="grid sm:grid-cols-4 gap-2 items-end">
                        @csrf
                        <div>
                            <label class="block mb-1">Kategori</label>
                            <select name="kategori" class="w-full rounded border-gray-300">
                                <option value="transport">Transport</option>
                                <option value="akomodasi">Akomodasi</option>
                                <option value="harian">Harian</option>
                                <option value="lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div>
                            <label class="block mb-1">Jumlah</label>
                            <input type="number" step="0.01" name="jumlah" class="w-full rounded border-gray-300" required />
                        </div>
                        <div>
                            <label class="block mb-1">Tanggal</label>
                            <input type="date" name="tanggal" class="w-full rounded border-gray-300" required />
                        </div>
                        <div>
                            <button class="px-4 py-2 bg-blue-600 text-white rounded">Tambah</button>
                        </div>
                    </form>
                </div>

                <div>
                    <h3 class="font-semibold mt-6 mb-2">Daftar Biaya</h3>
                    @if($sppd->expenses->count())
                        <table class="min-w-full text-left text-sm">
                            <thead>
                                <tr>
                                    <th class="px-3 py-2">Kategori</th>
                                    <th class="px-3 py-2">Jumlah</th>
                                    <th class="px-3 py-2">Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sppd->expenses as $e)
                                    <tr class="border-b border-gray-100 dark:border-gray-700">
                                        <td class="px-3 py-2">{{ $e->kategori }}</td>
                                        <td class="px-3 py-2">{{ number_format($e->jumlah, 2) }} {{ $e->mata_uang }}</td>
                                        <td class="px-3 py-2">{{ $e->tanggal->format('Y-m-d') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p>Belum ada biaya.</p>
                    @endif
                </div>

                <div>
                    <h3 class="font-semibold mt-6 mb-2">Unggah Lampiran</h3>
                    @if(auth()->user()->id === $sppd->pegawai_id || auth()->user()->role === 'admin')
                        <form method="POST" action="{{ route('sppd.attachments.store', $sppd) }}" enctype="multipart/form-data" class="grid sm:grid-cols-4 gap-2 items-end">
                            @csrf
                            <div>
                                <label class="block mb-1">Jenis</label>
                                <select name="jenis" class="w-full rounded border-gray-300 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100">
                                    <option value="surat_tugas">Surat Tugas</option>
                                    <option value="tiket">Tiket</option>
                                    <option value="bukti_biaya">Bukti Biaya</option>
                                    <option value="lainnya">Lainnya</option>
                                </select>
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block mb-1">File</label>
                                <input type="file" name="file" class="w-full rounded border-gray-300 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100" required />
                                <p class="text-xs text-slate-500">Maks 4MB, pdf/jpg/jpeg/png/webp</p>
                            </div>
                            <div>
                                <button class="inline-flex items-center justify-center px-4 py-2 rounded-md text-sm font-semibold shadow-sm ring-1 ring-inset bg-blue-600 text-white hover:bg-blue-700 ring-blue-700/20 dark:bg-blue-500 dark:hover:bg-blue-400 dark:ring-white/10">Unggah</button>
                            </div>
                        </form>
                    @endif

                    <h3 class="font-semibold mt-6 mb-2">Lampiran</h3>
                    @if($sppd->attachments->count())
                        <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($sppd->attachments as $a)
                                <li class="flex items-center justify-between py-2">
                                    <div>
                                        <span class="font-medium capitalize">{{ str_replace('_',' ', $a->jenis) }}</span>
                                        <span class="text-slate-500 ml-2 text-xs">{{ $a->mime }}, {{ number_format(($a->ukuran ?? 0)/1024, 1) }} KB</span>
                                        <a class="ml-3 text-blue-600 dark:text-blue-400 hover:underline" href="{{ asset('storage/'.$a->path) }}" target="_blank">Lihat</a>
                                    </div>
                                    @if(auth()->user()->id === $sppd->pegawai_id || auth()->user()->role === 'admin')
                                        <form method="POST" action="{{ route('sppd.attachments.destroy', [$sppd, $a]) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button class="inline-flex items-center justify-center px-3 py-2 rounded-md text-sm font-semibold shadow-sm ring-1 ring-inset bg-rose-600 text-white hover:bg-rose-700 ring-rose-700/20 dark:bg-rose-500 dark:hover:bg-rose-400 dark:ring-white/10">Hapus</button>
                                        </form>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p>Belum ada lampiran.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-dashboard-layout>
