<x-dashboard-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Detail SPPD</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow sm:rounded-lg p-8 ring-1 ring-gray-100 dark:ring-gray-700 text-gray-900 dark:text-gray-100 space-y-6">
                <div>
                    @php
                        $statusStyles = [
                            'draft' => 'bg-slate-100 text-slate-800',
                            'diajukan' => 'bg-amber-100 text-amber-800',
                            'disetujui_manager' => 'bg-blue-100 text-blue-800',
                            'disetujui' => 'bg-emerald-100 text-emerald-800',
                            'ditolak' => 'bg-rose-100 text-rose-800',
                            'selesai' => 'bg-indigo-100 text-indigo-800',
                        ];
                        $statusLabels = [
                            'draft' => 'Draft',
                            'diajukan' => 'Diajukan',
                            'disetujui_manager' => 'Disetujui Manager',
                            'disetujui' => 'Disetujui',
                            'ditolak' => 'Ditolak',
                            'selesai' => 'Selesai',
                        ];
                        $style = $statusStyles[$sppd->status] ?? 'bg-slate-100 text-slate-800';
                        $statusLabel = $statusLabels[$sppd->status] ?? ucfirst(str_replace('_', ' ', $sppd->status));
                    @endphp
                    <div class="grid sm:grid-cols-3 gap-4">
                        <div class="p-4 rounded bg-slate-50 dark:bg-slate-700">
                            <div class="text-xs uppercase text-slate-500">Kode</div>
                            <div class="text-base font-semibold">{{ $sppd->kode }}</div>
                        </div>
                        <div class="p-4 rounded bg-slate-50 dark:bg-slate-700 sm:col-span-1">
                            <div class="text-xs uppercase text-slate-500">Tujuan</div>
                            <div class="text-base font-semibold">{{ $sppd->tujuan }}</div>
                        </div>
                        <div class="p-4 rounded bg-slate-50 dark:bg-slate-700">
                            <div class="text-xs uppercase text-slate-500">Status</div>
                            <span class="inline-flex items-center text-sm font-semibold px-2.5 py-1 rounded-full {{ $style }}">{{ $statusLabel }}</span>
                        </div>
                    </div>
                </div>

                @php
                    $canManageDraftActions = auth()->user()->role === 'admin' && in_array($sppd->status, ['draft','ditolak']);
                    $canSeePdfAction = in_array(auth()->user()->role, ['admin','manager','direksi'], true);
                    $missingPdfRequirements = $sppd->missingPdfRequirements();
                    $pdfReady = $missingPdfRequirements === [];
                @endphp

                @if($errors->has('pdf'))
                    <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900 dark:border-amber-700/60 dark:bg-amber-900/20 dark:text-amber-100">
                        {{ $errors->first('pdf') }}
                    </div>
                @endif

                @if($sppd->status === 'ditolak' && filled($sppd->alasan_penolakan))
                    <div class="rounded-lg border border-rose-200 bg-rose-50 p-4 text-rose-900 dark:border-rose-700/60 dark:bg-rose-900/20 dark:text-rose-100">
                        <div class="text-sm font-semibold uppercase tracking-wide text-rose-700 dark:text-rose-300">Alasan Penolakan</div>
                        <p class="mt-2 text-sm leading-6">{{ $sppd->alasan_penolakan }}</p>
                    </div>
                @endif

                <div x-data="{ open: false }" class="border rounded-lg p-4 bg-slate-50 dark:bg-slate-800">
                    <button type="button" x-on:click="open = !open" class="w-full flex items-center justify-between">
                        <span class="font-semibold">Ringkasan Pengajuan</span>
                        <svg class="h-5 w-5 transition-transform" :class="open ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open" x-transition class="mt-4 space-y-4">
                        <div>
                            <h4 class="text-sm font-semibold mb-2">Tujuan & Lokasi</h4>
                            <div class="grid sm:grid-cols-2 gap-3 text-sm">
                                <div><span class="text-slate-500">Tujuan:</span> <span class="font-medium">{{ $sppd->tujuan }}</span></div>
                                <div><span class="text-slate-500">Kota:</span> <span class="font-medium">{{ $sppd->kota ?? '-' }}</span></div>
                                <div><span class="text-slate-500">Negara:</span> <span class="font-medium">{{ $sppd->negara ?? '-' }}</span></div>
                                <div><span class="text-slate-500">Jenis Perjalanan:</span> <span class="font-medium">{{ $sppd->jenis_perjalanan === 'diklat' ? 'Diklat' : 'Non Diklat' }}</span></div>
                                <div><span class="text-slate-500">Jenis:</span> <span class="font-medium">{{ $sppd->jenis_surat === 'undangan' ? 'Undangan' : 'Surat Tugas' }}</span></div>
                                <div><span class="text-slate-500">Pejabat Berwenang:</span> <span class="font-medium">{{ $sppd->pejabatPerintah?->name ?? '-' }}</span></div>
                                @if(!empty($sppd->sumber_anggaran))
                                    <div class="sm:col-span-2"><span class="text-slate-500">Sumber Anggaran:</span> <span class="font-medium">{{ $sppd->sumber_anggaran }}</span></div>
                                @endif
                            </div>
                            @if(auth()->user()->role === 'admin' && in_array($sppd->status, ['draft','ditolak']))
                                <details class="mt-3">
                                    <summary class="cursor-pointer text-sm text-blue-600">Edit Tujuan & Lokasi</summary>
                                    <form method="POST" action="{{ route('sppd.update', $sppd) }}" class="mt-2 grid sm:grid-cols-2 gap-3">
                                        @csrf
                                        @method('PATCH')
                                        <div>
                                            <label class="block mb-1 text-sm">Tujuan</label>
                                            <input name="tujuan" value="{{ $sppd->tujuan }}" class="w-full rounded border-gray-300 bg-white dark:bg-slate-800" />
                                        </div>
                                        <div>
                                            <label class="block mb-1 text-sm">Kota</label>
                                            <input name="kota" value="{{ $sppd->kota }}" class="w-full rounded border-gray-300 bg-white dark:bg-slate-800" />
                                        </div>
                                        <div>
                                            <label class="block mb-1 text-sm">Negara</label>
                                            <input name="negara" value="{{ $sppd->negara }}" class="w-full rounded border-gray-300 bg-white dark:bg-slate-800" />
                                        </div>
                                        <div>
                                            <label class="block mb-1 text-sm">Jenis Perjalanan</label>
                                            <select name="jenis_perjalanan" class="w-full rounded border-gray-300 bg-white dark:bg-slate-800">
                                                <option value="non_diklat" @selected($sppd->jenis_perjalanan==='non_diklat')>Non Diklat</option>
                                                <option value="diklat" @selected($sppd->jenis_perjalanan==='diklat')>Diklat</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block mb-1 text-sm">Jenis</label>
                                            <select name="jenis_surat" class="w-full rounded border-gray-300 bg-white dark:bg-slate-800">
                                                <option value="surat_tugas" @selected($sppd->jenis_surat==='surat_tugas')>Surat Tugas</option>
                                                <option value="undangan" @selected($sppd->jenis_surat==='undangan')>Undangan</option>
                                            </select>
                                        </div>
                                        <div class="sm:col-span-2">
                                            <label class="block mb-1 text-sm">Sumber Anggaran</label>
                                            <textarea name="sumber_anggaran" rows="3" class="w-full rounded border-gray-300 bg-white dark:bg-slate-800">{{ $sppd->sumber_anggaran }}</textarea>
                                        </div>
                                        <div class="sm:col-span-2">
                                            <button class="px-3 py-2 bg-blue-600 text-white rounded text-sm">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </details>
                            @endif
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold mb-2">Tanggal</h4>
                            <div class="grid sm:grid-cols-3 gap-3 text-sm">
                                <div><span class="text-slate-500">Berangkat:</span> <span class="font-medium">{{ $sppd->tanggal_berangkat->format('Y-m-d') }}</span></div>
                                <div><span class="text-slate-500">Pulang:</span> <span class="font-medium">{{ $sppd->tanggal_pulang->format('Y-m-d') }}</span></div>
                                <div><span class="text-slate-500">Lama:</span> <span class="font-medium">{{ $sppd->lama_hari }} hari</span></div>
                            </div>
                            @if(auth()->user()->role === 'admin' && in_array($sppd->status, ['draft','ditolak']))
                                <details class="mt-3">
                                    <summary class="cursor-pointer text-sm text-blue-600">Edit Tanggal</summary>
                                    <form method="POST" action="{{ route('sppd.update', $sppd) }}" class="mt-2 grid sm:grid-cols-3 gap-3">
                                        @csrf
                                        @method('PATCH')
                                        <div>
                                            <label class="block mb-1 text-sm">Berangkat</label>
                                            <input type="date" name="tanggal_berangkat" value="{{ $sppd->tanggal_berangkat->format('Y-m-d') }}" class="w-full rounded border-gray-300 bg-white dark:bg-slate-800" />
                                        </div>
                                        <div>
                                            <label class="block mb-1 text-sm">Pulang</label>
                                            <input type="date" name="tanggal_pulang" value="{{ $sppd->tanggal_pulang->format('Y-m-d') }}" class="w-full rounded border-gray-300 bg-white dark:bg-slate-800" />
                                        </div>
                                        <div>
                                            <label class="block mb-1 text-sm">Lama (hari)</label>
                                            <input type="number" name="lama_hari" value="{{ $sppd->lama_hari }}" min="1" class="w-full rounded border-gray-300 bg-white dark:bg-slate-800" />
                                        </div>
                                        <div class="sm:col-span-3">
                                            <button class="px-3 py-2 bg-blue-600 text-white rounded text-sm">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </details>
                            @endif
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold mb-2">Rincian</h4>
                            <div class="text-sm space-y-1">
                                <p>{{ $sppd->maksud_perjalanan }}</p>
                                @if(!empty($sppd->transportasi))
                                    <p><span class="font-semibold">Transportasi:</span> {{ $sppd->transportasi }}</p>
                                @endif
                                @if(!empty($sppd->anggota))
                                    <div class="pt-2">
                                        <p><span class="font-semibold">Anggota Perjalanan:</span></p>
                                        <ol class="mt-1 list-decimal list-inside space-y-1">
                                            @foreach($sppd->anggota as $nama)
                                                <li>{{ $nama }}</li>
                                            @endforeach
                                        </ol>
                                    </div>
                                @endif
                            </div>
                            @if(auth()->user()->role === 'admin' && in_array($sppd->status, ['draft','ditolak']))
                                <details class="mt-3">
                                    <summary class="cursor-pointer text-sm text-blue-600">Edit Rincian</summary>
                                    <form method="POST" action="{{ route('sppd.update', $sppd) }}"
                                          x-data="{ anggota: {{ Js::from($sppd->anggota ?? ['']) }}, employeeOptions: {{ Js::from($employeeOptions) }} }"
                                          class="mt-2 space-y-3">
                                        @csrf
                                        @method('PATCH')
                                        <textarea name="maksud_perjalanan" rows="4" class="w-full rounded border-gray-300 bg-white dark:bg-slate-800">{{ $sppd->maksud_perjalanan }}</textarea>
                                        <textarea name="transportasi" rows="3" class="w-full rounded border-gray-300 bg-white dark:bg-slate-800" placeholder="Transportasi yang digunakan">{{ $sppd->transportasi }}</textarea>
                                        <div class="border rounded-lg p-4 bg-slate-100 dark:bg-slate-900/40">
                                            <h5 class="text-sm font-semibold mb-3">Pegawai / Anggota Perjalanan</h5>
                                            <template x-for="(nama, i) in anggota" :key="i">
                                                <div class="flex gap-2 mb-2">
                                                    <select :name="'anggota[]'" x-model="anggota[i]"
                                                            class="flex-1 rounded border-gray-300 bg-white dark:bg-slate-700 text-sm">
                                                        <option value="">Pilih anggota</option>
                                                        <template x-for="employee in employeeOptions" :key="employee.id">
                                                            <option :value="employee.value" x-text="employee.value"></option>
                                                        </template>
                                                    </select>
                                                    <button type="button" x-show="anggota.length > 1"
                                                            x-on:click="anggota.splice(i, 1)"
                                                            class="px-2 py-1 text-rose-600 hover:text-rose-800 text-sm">✕</button>
                                                </div>
                                            </template>
                                            <button type="button" x-on:click="anggota.push('')"
                                                    class="mt-1 px-3 py-1 bg-indigo-600 text-white rounded text-sm">+ Tambah Pegawai</button>
                                        </div>
                                        <div class="mt-2">
                                            <button class="px-3 py-2 bg-blue-600 text-white rounded text-sm">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </details>
                            @endif
                        </div>
                    </div>
                </div>

                

                @if(auth()->user()->role === 'admin' && in_array($sppd->status, ['draft','diajukan','ditolak']))
                    <div>
                        <h3 class="font-semibold mb-2">Tambah Biaya</h3>
                        <form method="POST" action="{{ route('sppd.expenses.store', $sppd) }}" class="grid sm:grid-cols-5 gap-3 items-end rounded-xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-800/80">
                            @csrf
                            <div>
                                <label class="block mb-1 text-sm font-medium text-slate-700 dark:text-slate-200">Pegawai</label>
                                <select name="participant_name" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100 dark:[color-scheme:dark]" required>
                                    <option value="">Pilih pegawai</option>
                                    @foreach($participantOptions as $participant)
                                        <option value="{{ $participant }}">{{ $participant }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block mb-1 text-sm font-medium text-slate-700 dark:text-slate-200">Kategori</label>
                                <select name="kategori" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100 dark:[color-scheme:dark]">
                                    <option value="uang_makan">Uang Makan</option>
                                    <option value="cuci_pakaian">Cuci Pakaian</option>
                                </select>
                            </div>
                            <div>
                                <label class="block mb-1 text-sm font-medium text-slate-700 dark:text-slate-200">Rate</label>
                                <input type="number" step="0.01" name="jumlah" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-blue-500 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100" required />
                            </div>
                            <div>
                                <label class="block mb-1 text-sm font-medium text-slate-700 dark:text-slate-200">Jumlah Hari</label>
                                <input type="number" name="jumlah_hari" min="1" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-blue-500 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100" />
                                <input type="hidden" name="tanggal" value="{{ now()->toDateString() }}" />
                            </div>
                            <div>
                                <button class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700">Tambah</button>
                            </div>
                        </form>
                    </div>
                @endif

                <div>
                    <h3 class="font-semibold mt-6 mb-2">Daftar Biaya</h3>
                    @if($sppd->expenses->count())
                        <table class="min-w-full text-left text-sm">
                            <thead>
                                <tr>
                                    <th class="px-3 py-2">Pegawai</th>
                                    <th class="px-3 py-2">Kategori</th>
                                    <th class="px-3 py-2">Rate</th>
                                    <th class="px-3 py-2">Jumlah Hari</th>
                                    <th class="px-3 py-2 text-right">Total</th>
                                    @if(auth()->user()->role === 'admin' && in_array($sppd->status, ['draft','diajukan','ditolak']))
                                        <th class="px-3 py-2">Aksi</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sppd->expenses as $e)
                                    <tr class="border-b border-gray-100 dark:border-gray-700">
                                        <td class="px-3 py-2">{{ $e->participant_name ?: 'Semua Pegawai' }}</td>
                                        <td class="px-3 py-2">{{ $e->kategori }}</td>
                                        <td class="px-3 py-2">{{ number_format((float) $e->jumlah, 0, ',', '.') }} {{ $e->mata_uang }}</td>
                                        <td class="px-3 py-2">{{ $e->jumlah_hari }}</td>
                                        <td class="px-3 py-2 text-right">{{ number_format((float) $e->jumlah, 0, ',', '.') }}</td>
                                        @if(auth()->user()->role === 'admin' && in_array($sppd->status, ['draft','diajukan','ditolak']))
                                            <td class="px-3 py-2" x-data="{ open: false }">
                                                <button type="button" class="text-blue-600 hover:underline" x-on:click="open = !open">Aksi</button>
                                                <div x-show="open" x-transition class="mt-2 space-y-2">
                                                    <form method="POST" action="{{ route('sppd.expenses.update', [$sppd, $e]) }}" class="grid sm:grid-cols-5 gap-3 items-end rounded-xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-800/80">
                                                        @csrf
                                                        @method('PUT')
                                                        <div>
                                                            <label class="block mb-1 text-sm font-medium text-slate-700 dark:text-slate-200">Pegawai</label>
                                                            <select name="participant_name" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100 dark:[color-scheme:dark]" required>
                                                                <option value="">Pilih pegawai</option>
                                                                @foreach($participantOptions as $participant)
                                                                    <option value="{{ $participant }}" @selected($e->participant_name === $participant)>{{ $participant }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div>
                                                            <label class="block mb-1 text-sm font-medium text-slate-700 dark:text-slate-200">Kategori</label>
                                                            <select name="kategori" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100 dark:[color-scheme:dark]">
                                                                <option value="uang_makan" @selected($e->kategori==='uang_makan')>Uang Makan</option>
                                                                <option value="cuci_pakaian" @selected($e->kategori==='cuci_pakaian')>Cuci Pakaian</option>
                                                            </select>
                                                        </div>
                                                        <div>
                                                            <label class="block mb-1 text-sm font-medium text-slate-700 dark:text-slate-200">Rate</label>
                                                            <input type="number" step="0.01" name="jumlah" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100" value="{{ $e->jumlah }}" required />
                                                        </div>
                                                        <div>
                                                            <label class="block mb-1 text-sm font-medium text-slate-700 dark:text-slate-200">Jumlah Hari</label>
                                                            <input type="number" name="jumlah_hari" min="1" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100" value="{{ $e->jumlah_hari }}" />
                                                            <input type="hidden" name="tanggal" class="w-full rounded border-gray-300" value="{{ $e->tanggal->format('Y-m-d') }}" />
                                                        </div>
                                                        <div>
                                                            <button class="w-full px-3 py-2 bg-indigo-600 text-white rounded-lg font-semibold hover:bg-indigo-700">Simpan</button>
                                                        </div>
                                                    </form>
                                                    <form method="POST" action="{{ route('sppd.expenses.destroy', [$sppd, $e]) }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="px-3 py-2 bg-rose-600 text-white rounded">Hapus</button>
                                                    </form>
                                                </div>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p>Belum ada biaya.</p>
                    @endif
                </div>
                @if($canSeePdfAction && ! $canManageDraftActions)
                    <div>
                        @if($pdfReady)
                            <a href="{{ route('sppd.pdf', $sppd) }}" class="inline-flex items-center justify-center px-4 py-2 rounded-md text-sm font-semibold shadow-sm ring-1 ring-inset bg-emerald-600 text-white hover:bg-emerald-700 ring-emerald-700/20 dark:bg-emerald-500 dark:hover:bg-emerald-400 dark:ring-white/10">Unduh PDF</a>
                        @else
                            <button type="button" disabled class="inline-flex items-center justify-center px-4 py-2 rounded-md text-sm font-semibold shadow-sm ring-1 ring-inset bg-slate-300 text-slate-500 cursor-not-allowed ring-slate-300 dark:bg-slate-700 dark:text-slate-400 dark:ring-white/10">Unduh PDF</button>
                            <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Lengkapi dulu: {{ implode(', ', $missingPdfRequirements) }}.</p>
                        @endif
                    </div>
                @endif
                @if($canManageDraftActions)
                    <div class="mt-6">
                        <h3 class="font-semibold mb-2">Pejabat Berwenang Memberi Perintah</h3>
                        <form method="POST" action="{{ route('sppd.update', $sppd) }}" class="max-w-lg">
                            @csrf
                            @method('PATCH')
                            <label class="block mb-1 text-sm">Pilih Pejabat (Admin/Manager/Direksi)</label>
                            <select name="pejabat_perintah_id" class="w-full rounded border-gray-300 bg-white dark:bg-slate-800">
                                <option value="">-</option>
                                @foreach($officers as $u)
                                    <option value="{{ $u->id }}" @selected($sppd->pejabat_perintah_id===$u->id)>{{ $u->name }} ({{ ucfirst($u->role) }})</option>
                                @endforeach
                            </select>
                            <div class="mt-3">
                                <button class="px-3 py-2 bg-blue-600 text-white rounded text-sm">Simpan Pejabat</button>
                            </div>
                        </form>
                    </div>
                @endif

                @if(auth()->user()->role === 'manager' && $sppd->status === 'diajukan')
                    <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <div class="bg-slate-50 dark:bg-slate-800 rounded-lg p-4 ring-1 ring-gray-100 dark:ring-gray-700">
                            <h3 class="font-semibold mb-3">Keputusan Manager</h3>
                            <div class="flex flex-wrap gap-3">
                                <form method="POST" action="{{ route('sppd.setujui', $sppd) }}">
                                    @csrf
                                    <button class="inline-flex items-center justify-center px-4 py-2 rounded-md text-sm font-semibold shadow-sm ring-1 ring-inset bg-emerald-600 text-white hover:bg-emerald-700 ring-emerald-700/20 dark:bg-emerald-500 dark:hover:bg-emerald-400 dark:ring-white/10">Setujui</button>
                                </form>
                                <form method="POST" action="{{ route('sppd.tolak', $sppd) }}" class="flex items-center gap-2">
                                    @csrf
                                    <input type="text" name="alasan_penolakan" class="rounded border-gray-300 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100" placeholder="Alasan penolakan" required />
                                    <button class="inline-flex items-center justify-center px-4 py-2 rounded-md text-sm font-semibold shadow-sm ring-1 ring-inset bg-rose-600 text-white hover:bg-rose-700 ring-rose-700/20 dark:bg-rose-500 dark:hover:bg-rose-400 dark:ring-white/10">Tolak</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
                @if(auth()->user()->role === 'direksi' && $sppd->status === 'disetujui_manager')
                    <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <div class="bg-slate-50 dark:bg-slate-800 rounded-lg p-4 ring-1 ring-gray-100 dark:ring-gray-700">
                            <h3 class="font-semibold mb-3">Keputusan Direksi</h3>
                            <div class="flex flex-wrap gap-3">
                                <form method="POST" action="{{ route('sppd.setujui', $sppd) }}">
                                    @csrf
                                    <button class="inline-flex items-center justify-center px-4 py-2 rounded-md text-sm font-semibold shadow-sm ring-1 ring-inset bg-emerald-600 text-white hover:bg-emerald-700 ring-emerald-700/20 dark:bg-emerald-500 dark:hover:bg-emerald-400 dark:ring-white/10">Setujui</button>
                                </form>
                                <form method="POST" action="{{ route('sppd.tolak', $sppd) }}" class="flex items-center gap-2">
                                    @csrf
                                    <input type="text" name="alasan_penolakan" class="rounded border-gray-300 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100" placeholder="Alasan penolakan" required />
                                    <button class="inline-flex items-center justify-center px-4 py-2 rounded-md text-sm font-semibold shadow-sm ring-1 ring-inset bg-rose-600 text-white hover:bg-rose-700 ring-rose-700/20 dark:bg-rose-500 dark:hover:bg-rose-400 dark:ring-white/10">Tolak</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif

                <div>
                    <h3 class="font-semibold mt-6 mb-2">Unggah Lampiran</h3>
                    @if(auth()->user()->role === 'admin')
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
                                        <a class="ml-3 text-blue-600 dark:text-blue-400 hover:underline" href="{{ route('sppd.attachments.show', [$sppd, $a]) }}" target="_blank">Lihat</a>
                                    </div>
                                    @if(auth()->user()->role === 'admin')
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
                {{-- Members Management --}}
                <div class="border rounded-lg p-4 bg-slate-50 dark:bg-slate-800">
                    <h3 class="font-semibold mb-3">Anggota Perjalanan</h3>
                    @if(!empty($sppd->anggota))
                        <ol class="list-decimal list-inside space-y-1 text-sm mb-3">
                            @foreach($sppd->anggota as $nama)
                                <li>{{ $nama }}</li>
                            @endforeach
                        </ol>
                    @else
                        <p class="text-sm text-slate-400 mb-3">Belum ada anggota.</p>
                    @endif
                    @if(auth()->user()->role === 'admin' && in_array($sppd->status, ['draft','ditolak']))
                        <details>
                            <summary class="cursor-pointer text-sm text-blue-600">Edit Anggota</summary>
                            <form method="POST" action="{{ route('sppd.update', $sppd) }}"
                                  x-data="{ anggota: {{ Js::from($sppd->anggota ?? ['']) }}, employeeOptions: {{ Js::from($employeeOptions) }} }"
                                  class="mt-3 space-y-2">
                                @csrf
                                @method('PATCH')
                                <template x-for="(nama, i) in anggota" :key="i">
                                    <div class="flex gap-2 mb-2">
                                        <select :name="'anggota[]'" x-model="anggota[i]"
                                                class="flex-1 rounded border-gray-300 bg-white dark:bg-slate-700 text-sm">
                                            <option value="">Pilih anggota</option>
                                            <template x-for="employee in employeeOptions" :key="employee.id">
                                                <option :value="employee.value" x-text="employee.value"></option>
                                            </template>
                                        </select>
                                        <button type="button" x-show="anggota.length > 1"
                                                x-on:click="anggota.splice(i, 1)"
                                                class="px-2 py-1 text-rose-600 hover:text-rose-800 text-sm">✕</button>
                                    </div>
                                </template>
                                <div class="flex gap-2 items-center">
                                    <button type="button" x-on:click="anggota.push('')"
                                            class="px-3 py-1 bg-indigo-600 text-white rounded text-sm">+ Tambah</button>
                                    <button class="px-3 py-2 bg-blue-600 text-white rounded text-sm">Simpan Anggota</button>
                                </div>
                            </form>
                        </details>
                    @endif
                </div>

                @if($canManageDraftActions)
                    <div class="flex gap-3 flex-wrap items-start pt-6 border-t border-gray-200 dark:border-gray-700">
                        <form method="POST" action="{{ route('sppd.ajukan', $sppd) }}">
                            @csrf
                            <button class="inline-flex items-center justify-center px-4 py-2 rounded-md text-sm font-semibold shadow-sm ring-1 ring-inset bg-blue-600 text-white hover:bg-blue-700 ring-blue-700/20 dark:bg-blue-500 dark:hover:bg-blue-400 dark:ring-white/10">Ajukan</button>
                        </form>
                        @if($canSeePdfAction)
                            <div>
                                @if($pdfReady)
                                    <a href="{{ route('sppd.pdf', $sppd) }}" class="inline-flex items-center justify-center px-4 py-2 rounded-md text-sm font-semibold shadow-sm ring-1 ring-inset bg-emerald-600 text-white hover:bg-emerald-700 ring-emerald-700/20 dark:bg-emerald-500 dark:hover:bg-emerald-400 dark:ring-white/10">Unduh PDF</a>
                                @else
                                    <button type="button" disabled class="inline-flex items-center justify-center px-4 py-2 rounded-md text-sm font-semibold shadow-sm ring-1 ring-inset bg-slate-300 text-slate-500 cursor-not-allowed ring-slate-300 dark:bg-slate-700 dark:text-slate-400 dark:ring-white/10">Unduh PDF</button>
                                    <p class="mt-2 max-w-xl text-xs text-slate-500 dark:text-slate-400">Lengkapi dulu: {{ implode(', ', $missingPdfRequirements) }}.</p>
                                @endif
                            </div>
                        @endif
                        @if($sppd->status === 'ditolak')
                            <form method="POST" action="{{ route('sppd.ajukanUlang', $sppd) }}">
                                @csrf
                                <button class="inline-flex items-center justify-center px-4 py-2 rounded-md text-sm font-semibold shadow-sm ring-1 ring-inset bg-indigo-600 text-white hover:bg-indigo-700 ring-indigo-700/20 dark:bg-indigo-500 dark:hover:bg-indigo-400 dark:ring-white/10">Ajukan Ulang</button>
                            </form>
                        @endif
                    </div>
                @endif

            </div>
        </div>
    </div>
</x-dashboard-layout>
