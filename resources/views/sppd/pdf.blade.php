<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 6px; margin-bottom: 12px; }
        .header h1 { margin: 0; font-size: 16px; }
        .header p { margin: 2px 0; }
        .section-title { font-weight: bold; margin: 12px 0 6px; }
        .grid { width: 100%; border-collapse: collapse; }
        .grid td { padding: 6px; vertical-align: top; }
        .grid .label { width: 28%; color: #444; }
        table.items { width: 100%; border-collapse: collapse; }
        table.items th, table.items td { border: 1px solid #999; padding: 6px; }
        table.items th { background: #eee; }
        .footer { margin-top: 24px; display: flex; justify-content: space-between; }
        .sig { width: 45%; text-align: center; }
        .muted { color: #555; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Perjalanan Dinas (SPPD)</h1>
        <p class="muted">Kode: {{ $sppd->kode }}</p>
    </div>

    <div>
        <div class="section-title">A. Informasi Umum</div>
        <table class="grid">
            <tr>
                <td class="label">Pemohon</td>
                <td>{{ $sppd->pegawai?->name }} ({{ $sppd->pegawai?->email }})</td>
            </tr>
            <tr>
                <td class="label">Tujuan</td>
                <td>{{ $sppd->tujuan }}</td>
            </tr>
            <tr>
                <td class="label">Lokasi</td>
                <td>{{ $sppd->kota ?? '-' }}, {{ $sppd->negara ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Jenis Perjalanan</td>
                <td>{{ $sppd->jenis_perjalanan === 'diklat' ? 'Diklat' : 'Non Diklat' }}</td>
            </tr>
            @if(!empty($sppd->sumber_anggaran))
            <tr>
                <td class="label">Sumber Anggaran</td>
                <td>{{ $sppd->sumber_anggaran }}</td>
            </tr>
            @endif
            <tr>
                <td class="label">Pejabat Berwenang</td>
                <td>{{ $sppd->pejabatPerintah?->name ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Periode</td>
                <td>{{ $sppd->tanggal_berangkat->format('Y-m-d') }} s/d {{ $sppd->tanggal_pulang->format('Y-m-d') }} ({{ $sppd->lama_hari }} hari)</td>
            </tr>
            <tr>
                <td class="label">Status</td>
                <td>{{ ucfirst($sppd->status) }}</td>
            </tr>
            <tr>
                <td class="label">Maksud Perjalanan</td>
                <td>{{ $sppd->maksud_perjalanan }}</td>
            </tr>
        </table>
    </div>

    <div>
        <div class="section-title">B. Rincian Biaya</div>
        @if($sppd->expenses->count())
            @php $total = 0; @endphp
            <table class="items">
                <thead>
                    <tr>
                        <th style="width:30%">Kategori</th>
                        <th style="width:40%">Deskripsi</th>
                        <th style="width:15%; text-align:right">Jumlah</th>
                        <th style="width:15%">Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sppd->expenses as $e)
                        @php $total += (float) $e->jumlah; @endphp
                        <tr>
                            <td>{{ ucfirst(str_replace('_',' ', $e->kategori)) }}</td>
                            <td>{{ $e->deskripsi ?? '-' }}</td>
                            <td style="text-align:right">{{ number_format($e->jumlah, 2) }} {{ $e->mata_uang }}</td>
                            <td>{{ $e->tanggal->format('Y-m-d') }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <th colspan="2" style="text-align:right">TOTAL</th>
                        <th style="text-align:right">{{ number_format($total, 2) }} IDR</th>
                        <th></th>
                    </tr>
                </tbody>
            </table>
        @else
            <p class="muted">Tidak ada biaya.</p>
        @endif
    </div>

    <div class="footer">
        <div class="sig">
            <div class="muted">Pemohon</div>
            <br><br><br>
            <div><strong>{{ $sppd->pegawai?->name }}</strong></div>
        </div>
        <div class="sig">
            <div class="muted">Pejabat Berwenang</div>
            <br><br><br>
            <div><strong>{{ $sppd->pejabatPerintah?->name ?? '________________' }}</strong></div>
        </div>
    </div>
</body>
</html>
