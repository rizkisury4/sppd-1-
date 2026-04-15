<x-dashboard-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Buat SPPD</h2>
    </x-slot>

    <div x-data="sppdWizard()" class="max-w-7xl mx-auto">
        <div class="bg-white dark:bg-gray-800 shadow rounded-xl ring-1 ring-gray-100 dark:ring-gray-700 overflow-hidden">
            <div class="px-6 pt-6">
                <div class="relative">
                    <div class="absolute top-1/2 -translate-y-1/2 h-0.5 bg-gray-200 dark:bg-gray-700"
                         style="left:12.5%; right:12.5%;"></div>
                    <div class="absolute top-1/2 -translate-y-1/2 h-0.5 bg-indigo-600"
                         :style="{ left: '12.5%', width: progressFill() }"></div>
                    <div class="grid grid-cols-4 gap-0 text-sm font-medium text-gray-600 dark:text-gray-300">
                        <div class="flex flex-col items-center">
                            <div :class="step>=1 ? 'bg-indigo-600 text-white ring-indigo-600/30' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 ring-transparent'"
                                 class="h-8 w-8 rounded-full flex items-center justify-center ring-4">1</div>
                            <span class="mt-2 text-center hidden sm:block"
                                  :class="step>=1 ? 'text-indigo-600 dark:text-indigo-400' : ''">Tujuan & Lokasi</span>
                        </div>
                        <div class="flex flex-col items-center">
                            <div :class="step>=2 ? 'bg-indigo-600 text-white ring-indigo-600/30' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 ring-transparent'"
                                 class="h-8 w-8 rounded-full flex items-center justify-center ring-4">2</div>
                            <span class="mt-2 text-center hidden sm:block"
                                  :class="step>=2 ? 'text-indigo-600 dark:text-indigo-400' : ''">Tanggal</span>
                        </div>
                        <div class="flex flex-col items-center">
                            <div :class="step>=3 ? 'bg-indigo-600 text-white ring-indigo-600/30' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 ring-transparent'"
                                 class="h-8 w-8 rounded-full flex items-center justify-center ring-4">3</div>
                            <span class="mt-2 text-center hidden sm:block"
                                  :class="step>=3 ? 'text-indigo-600 dark:text-indigo-400' : ''">Rincian</span>
                        </div>
                        <div class="flex flex-col items-center">
                            <div :class="step>=4 ? 'bg-indigo-600 text-white ring-indigo-600/30' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 ring-transparent'"
                                 class="h-8 w-8 rounded-full flex items-center justify-center ring-4">4</div>
                            <span class="mt-2 text-center hidden sm:block"
                                  :class="step>=4 ? 'text-indigo-600 dark:text-indigo-400' : ''">Konfirmasi</span>
                        </div>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('sppd.store') }}" class="p-6 space-y-6 text-gray-900 dark:text-gray-100">
                @csrf

                <!-- Step 1 -->
                <div x-show="step===1" x-cloak>
                    <div class="space-y-4">
                        <div class="sm:col-span-2">
                            <label class="block mb-1">Tujuan</label>
                            <input x-model="form.tujuan" name="tujuan" class="w-full rounded border-gray-300 bg-white dark:bg-slate-800" required />
                        </div>
                        <div>
                            <label class="block mb-1">Jenis Perjalanan Dinas</label>
                            <select x-model="form.jenis_perjalanan" name="jenis_perjalanan" class="w-full rounded border-gray-300 bg-white dark:bg-slate-800" required>
                                <option value="non_diklat">Non Diklat</option>
                                <option value="diklat">Diklat</option>
                            </select>
                        </div>
                        <div>
                            <label class="block mb-1">Jenis</label>
                            <select x-model="form.jenis_surat" name="jenis_surat" class="w-full rounded border-gray-300 bg-white dark:bg-slate-800">
                                <option value="surat_tugas">Surat Tugas</option>
                                <option value="undangan">Undangan</option>
                            </select>
                        </div>
                        <div class="relative">
                            <label class="block mb-1">Kota Asal (Indonesia)</label>
                            <input x-model="form.origin_query"
                                   x-on:input.debounce.300ms="loadOrigin()"
                                   placeholder="Cari kota asal..."
                                   class="w-full rounded border-gray-300 bg-white dark:bg-slate-800" />
                            <ul x-show="originOptions.length"
                                class="absolute mt-1 w-full bg-white dark:bg-slate-800 border border-gray-200 dark:border-gray-700 rounded shadow z-10 max-h-56 overflow-auto">
                                <template x-for="opt in originOptions" :key="opt.name">
                                    <li x-on:click="selectOrigin(opt)"
                                        class="px-3 py-2 hover:bg-gray-100 dark:hover:bg-slate-700 cursor-pointer"
                                        x-text="opt.name"></li>
                                </template>
                            </ul>
                            <p class="text-xs text-gray-500 mt-1" x-show="form.origin">Dipilih: <span x-text="form.origin?.name"></span></p>
                        </div>
                        <div class="relative">
                            <label class="block mb-1">Kota Tujuan (Indonesia)</label>
                            <input x-model="form.dest_query"
                                   x-on:input.debounce.300ms="loadDest()"
                                   placeholder="Cari kota tujuan..."
                                   class="w-full rounded border-gray-300 bg-white dark:bg-slate-800" />
                            <ul x-show="destOptions.length"
                                class="absolute mt-1 w-full bg-white dark:bg-slate-800 border border-gray-200 dark:border-gray-700 rounded shadow z-10 max-h-56 overflow-auto">
                                <template x-for="opt in destOptions" :key="opt.name">
                                    <li x-on:click="selectDest(opt)"
                                        class="px-3 py-2 hover:bg-gray-100 dark:hover:bg-slate-700 cursor-pointer"
                                        x-text="opt.name"></li>
                                </template>
                            </ul>
                            <p class="text-xs text-gray-500 mt-1" x-show="form.dest">Dipilih: <span x-text="form.dest?.name"></span></p>
                        </div>
                        <div>
                            <div class="text-sm text-gray-700 dark:text-gray-300" x-show="form.distance_km !== null">
                                Perkiraan jarak: <span class="font-semibold" x-text="(form.distance_km ?? 0).toFixed(1)"></span> km
                            </div>
                        </div>
                        <div>
                            <label class="block mb-1">Sumber Anggaran</label>
                            <textarea x-model="form.sumber_anggaran" name="sumber_anggaran" rows="4" class="w-full rounded border-gray-300 bg-white dark:bg-slate-800" placeholder="Contoh: DIPA 2026 Kegiatan X ..."></textarea>
                        </div>
                        <input type="hidden" name="kota" x-model="form.kota" />
                        <input type="hidden" name="negara" x-model="form.negara" />
                    </div>
                </div>

                <!-- Step 2 -->
                <div x-show="step===2" x-cloak>
                    <div class="space-y-4">
                        <div>
                            <label class="block mb-1">Tanggal Berangkat</label>
                            <input x-model="form.tanggal_berangkat" type="date" name="tanggal_berangkat" class="w-full rounded border-gray-300 bg-white dark:bg-slate-800" required />
                        </div>
                        <div>
                            <label class="block mb-1">Tanggal Pulang</label>
                            <input x-model="form.tanggal_pulang" type="date" name="tanggal_pulang" class="w-full rounded border-gray-300 bg-white dark:bg-slate-800" required />
                        </div>
                    </div>
                    <div class="mt-4">
                        <label class="block mb-1">Lama (hari)</label>
                        <input x-model.number="form.lama_hari" type="number" name="lama_hari" min="1" class="w-full rounded border-gray-300 bg-white dark:bg-slate-800" required />
                        <p class="text-xs text-gray-500 mt-1">Otomatis dihitung berdasarkan tanggal, bisa disesuaikan.</p>
                    </div>
                </div>

                <!-- Step 3 -->
                <div x-show="step===3" x-cloak>
                    <div class="space-y-4">
                        <label class="block mb-1">Maksud Perjalanan</label>
                        <textarea x-model="form.maksud_perjalanan" name="maksud_perjalanan" class="w-full rounded border-gray-300 bg-white dark:bg-slate-800" rows="5" required></textarea>
                        <div>
                            <label class="block mb-1">Transportasi yang Digunakan</label>
                            <textarea x-model="form.transportasi" name="transportasi" class="w-full rounded border-gray-300 bg-white dark:bg-slate-800" rows="4" placeholder="Contoh: Pesawat Garuda, Taksi bandara, Kereta api..."></textarea>
                        </div>
                    </div>
                </div>

                <!-- Step 4 -->
                <div x-show="step===4" x-cloak class="space-y-3 text-sm">
                    <div class="rounded-lg border border-gray-200 dark:border-gray-700 divide-y divide-gray-200 dark:divide-gray-700">
                        <div class="p-4"><span class="font-semibold">Tujuan:</span> <span x-text="form.tujuan || '-'"></span></div>
                        <div class="p-4 grid grid-cols-2 gap-2">
                            <div><span class="font-semibold">Asal:</span> <span x-text="form.origin?.name || '-'"></span></div>
                            <div><span class="font-semibold">Tujuan:</span> <span x-text="form.dest?.name || '-'"></span></div>
                        </div>
                        <div class="p-4" x-show="form.distance_km !== null"><span class="font-semibold">Perkiraan jarak:</span> <span x-text="(form.distance_km ?? 0).toFixed(1)"></span> km</div>
                        <div class="p-4 grid grid-cols-3 gap-2">
                            <div><span class="font-semibold">Berangkat:</span> <span x-text="form.tanggal_berangkat || '-'"></span></div>
                            <div><span class="font-semibold">Pulang:</span> <span x-text="form.tanggal_pulang || '-'"></span></div>
                            <div><span class="font-semibold">Lama:</span> <span x-text="form.lama_hari || '-'"></span> hari</div>
                        </div>
                        <div class="p-4"><span class="font-semibold">Jenis Perjalanan:</span> <span x-text="form.jenis_perjalanan === 'diklat' ? 'Diklat' : 'Non Diklat'"></span></div>
                        <div class="p-4"><span class="font-semibold">Jenis:</span> <span x-text="form.jenis_surat === 'undangan' ? 'Undangan' : 'Surat Tugas'"></span></div>
                        <div class="p-4"><span class="font-semibold">Maksud:</span> <span x-text="form.maksud_perjalanan || '-'"></span></div>
                        <div class="p-4"><span class="font-semibold">Sumber Anggaran:</span> <span x-text="form.sumber_anggaran || '-'"></span></div>
                        <div class="p-4"><span class="font-semibold">Transportasi:</span> <span x-text="form.transportasi || '-'"></span></div>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-2">
                    <button type="button" x-on:click="prev()" x-show="step>1"
                            class="inline-flex items-center justify-center px-4 py-2 rounded-md text-sm font-semibold ring-1 ring-inset bg-slate-100 text-slate-800 hover:bg-slate-200 ring-slate-300 dark:bg-slate-700 dark:text-slate-100 dark:hover:bg-slate-600 dark:ring-white/10">
                        Sebelumnya
                    </button>
                    <div class="flex-1"></div>
                    <button type="button" x-on:click="next()" x-show="step<4"
                            class="inline-flex items-center justify-center px-4 py-2 rounded-md text-sm font-semibold ring-1 ring-inset bg-indigo-600 text-white hover:bg-indigo-700 ring-indigo-700/20 dark:bg-indigo-500 dark:hover:bg-indigo-400 dark:ring-white/10">
                        Lanjut
                    </button>
                    <button x-show="step===4" class="inline-flex items-center justify-center px-4 py-2 rounded-md text-sm font-semibold shadow-sm ring-1 ring-inset bg-blue-600 text-white hover:bg-blue-700 ring-blue-700/20 dark:bg-blue-500 dark:hover:bg-blue-400 dark:ring-white/10">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function sppdWizard() {
            return {
                step: 1,
                originOptions: [],
                destOptions: [],
                form: {
                    tujuan: '',
                    kota: '',
                    negara: 'Indonesia',
                    jenis_perjalanan: 'non_diklat',
                    jenis_surat: 'surat_tugas',
                    tanggal_berangkat: '',
                    tanggal_pulang: '',
                    lama_hari: 1,
                    maksud_perjalanan: '',
                    sumber_anggaran: '',
                    transportasi: '',
                    origin_query: '',
                    dest_query: '',
                    origin: null,
                    dest: null,
                    distance_km: null,
                },
                next() {
                    if (this.step === 1) {
                        if (!this.form.tujuan || !this.form.dest) return;
                    } else if (this.step === 2) {
                        if (!this.form.tanggal_berangkat || !this.form.tanggal_pulang || !this.form.lama_hari) return;
                    } else if (this.step === 3) {
                        if (!this.form.maksud_perjalanan) return;
                    }
                    if (this.step < 4) this.step++;
                },
                prev() {
                    if (this.step > 1) this.step--;
                },
                init() {
                    this.$watch('form.tanggal_berangkat', () => this.calcDays());
                    this.$watch('form.tanggal_pulang', () => this.calcDays());
                },
                calcDays() {
                    if (this.form.tanggal_berangkat && this.form.tanggal_pulang) {
                        const a = new Date(this.form.tanggal_berangkat);
                        const b = new Date(this.form.tanggal_pulang);
                        const diff = Math.floor((b - a) / (1000*60*60*24)) + 1;
                        this.form.lama_hari = isFinite(diff) && diff >= 1 ? diff : this.form.lama_hari;
                    }
                },
                progressFill() {
                    const w = (this.step - 1) * 25;
                    const clamped = Math.max(0, Math.min(75, w));
                    return clamped + '%';
                },
                async loadOrigin() {
                    if (!this.form.origin_query || this.form.origin_query.length < 3) { this.originOptions = []; return; }
                    const res = await fetch(`/geo/cities?q=${encodeURIComponent(this.form.origin_query)}`);
                    this.originOptions = await res.json();
                },
                async loadDest() {
                    if (!this.form.dest_query || this.form.dest_query.length < 3) { this.destOptions = []; return; }
                    const res = await fetch(`/geo/cities?q=${encodeURIComponent(this.form.dest_query)}`);
                    this.destOptions = await res.json();
                },
                selectOrigin(opt) {
                    this.form.origin = opt;
                    this.form.origin_query = opt.name;
                    this.originOptions = [];
                    this.calcDistance();
                },
                selectDest(opt) {
                    this.form.dest = opt;
                    this.form.dest_query = opt.name;
                    this.destOptions = [];
                    this.form.kota = opt.name;
                    this.form.negara = 'Indonesia';
                    this.calcDistance();
                },
                calcDistance() {
                    if (!this.form.origin || !this.form.dest) { this.form.distance_km = null; return; }
                    const R = 6371; // km
                    const toRad = d => d * Math.PI / 180;
                    const φ1 = toRad(this.form.origin.lat), φ2 = toRad(this.form.dest.lat);
                    const Δφ = toRad(this.form.dest.lat - this.form.origin.lat);
                    const Δλ = toRad(this.form.dest.lon - this.form.origin.lon);
                    const a = Math.sin(Δφ/2) * Math.sin(Δφ/2) +
                              Math.cos(φ1) * Math.cos(φ2) *
                              Math.sin(Δλ/2) * Math.sin(Δλ/2);
                    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
                    this.form.distance_km = R * c;
                }
            }
        }
    </script>
</x-dashboard-layout>
