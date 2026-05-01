<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1f2937; margin: 0; padding: 0; }
        .wrapper { padding: 6px 10px 0; }
        .title { text-align: center; font-size: 22px; font-weight: bold; margin-top: 6px; }
        .subtitle { text-align: center; color: #6b7280; font-size: 12px; margin-top: 4px; }
        .divider { border-top: 3px solid #444; margin: 12px 0 18px; }
        .section-title { font-size: 18px; font-weight: bold; margin: 10px 0 8px; }
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        .info-table td { padding: 5px 8px; vertical-align: top; }
        .info-label { width: 28%; color: #374151; }
        .info-value { width: 72%; font-weight: 500; }
        .items { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .items th, .items td { border: 1px solid #333; padding: 6px 5px; vertical-align: middle; }
        .items th { background: #ececec; font-weight: bold; text-align: center; }
        .items td { background: #fff; }
        .items .right { text-align: right; }
        .items .center { text-align: center; }
        .items .left { text-align: left; }
        .items .total-row td { font-weight: bold; background: #f3f4f6; }
        .signatures { width: 100%; margin-top: 28px; border-collapse: collapse; }
        .signatures td { width: 33.33%; text-align: center; vertical-align: top; }
        .sign-role { color: #6b7280; }
        .sign-space { height: 70px; }
        .sign-name { font-weight: bold; }
    </style>
</head>
<body>
    @php
        $expenses = $sppd->expenses;
        $members = collect($sppd->anggota ?? [])->filter()->values();

        if ($members->isEmpty()) {
            $members = $expenses->pluck('participant_name')->filter()->unique()->values();
        }

        $totalBiaya = 0;
        $pegawaiName = $sppd->pegawai?->employee?->name ?? $sppd->pegawai?->name;
        $pegawaiEmail = $sppd->pegawai?->email;
        $adminSigner = $pegawaiName ?? 'Administrator';
        $managerApproval = $sppd->approvals->first(function ($approval) {
            return $approval->catatan === 'Disetujui Manager' && $approval->approver;
        });
        $direksiApproval = $sppd->approvals->first(function ($approval) {
            return $approval->catatan === 'Disetujui Direksi' && $approval->approver;
        });
        $managerSigner = $managerApproval?->approver?->name ?? $sppd->pejabatPerintah?->name ?? 'Manager';
        $direksiSigner = $direksiApproval?->approver?->name ?? 'Direksi';
        $statusLabel = ucfirst(str_replace('_', ' ', $sppd->status ?? 'draft'));

    @endphp

    <div class="wrapper">
        <div class="title">Laporan Perjalanan Dinas (SPPD)</div>
        <div class="subtitle">Kode: {{ $sppd->kode }}</div>
        <div class="divider"></div>

        <div class="section-title">A. Informasi Umum</div>
        <table class="info-table">
            <tr>
                <td class="info-label">Pemohon</td>
                <td class="info-value">{{ $pegawaiName ?? '-' }}@if($pegawaiEmail) ({{ $pegawaiEmail }})@endif</td>
            </tr>
            <tr>
                <td class="info-label">Tujuan</td>
                <td class="info-value">{{ $sppd->tujuan ?? '-' }}</td>
            </tr>
            <tr>
                <td class="info-label">Lokasi</td>
                <td class="info-value">{{ collect([$sppd->kota, $sppd->negara])->filter()->implode(', ') ?: '-' }}</td>
            </tr>
            <tr>
                <td class="info-label">Jenis Perjalanan</td>
                <td class="info-value">{{ $sppd->jenis_perjalanan === 'diklat' ? 'Diklat' : 'Non Diklat' }}</td>
            </tr>
            <tr>
                <td class="info-label">Jenis</td>
                <td class="info-value">{{ $sppd->jenis_surat === 'undangan' ? 'Undangan' : 'Surat Tugas' }}</td>
            </tr>
            <tr>
                <td class="info-label">Sumber Anggaran</td>
                <td class="info-value">{{ $sppd->sumber_anggaran ?: '-' }}</td>
            </tr>
            <tr>
                <td class="info-label">Pejabat Berwenang</td>
                <td class="info-value">{{ $managerSigner }}</td>
            </tr>
            <tr>
                <td class="info-label">Periode</td>
                <td class="info-value">
                    {{ $sppd->tanggal_berangkat?->format('Y-m-d') ?? '-' }} s/d {{ $sppd->tanggal_pulang?->format('Y-m-d') ?? '-' }}
                    ({{ (int) ($sppd->lama_hari ?? 0) }} hari)
                </td>
            </tr>
            <tr>
                <td class="info-label">Status</td>
                <td class="info-value">{{ $statusLabel }}</td>
            </tr>
            <tr>
                <td class="info-label">Maksud Perjalanan</td>
                <td class="info-value">{{ $sppd->maksud_perjalanan ?: '-' }}</td>
            </tr>
        </table>

        <div class="section-title">B. Rincian Biaya</div>
        <table class="items">
            <thead>
                <tr>
                    <th rowspan="2" style="width:4%;">No</th>
                    <th rowspan="2" style="width:18%;">NAMA</th>
                    <th colspan="3">UANG MAKAN</th>
                    <th colspan="3">Cuci Pakaian</th>
                    <th rowspan="2" style="width:11%;">Transportasi<br>Lokal</th>
                    <th rowspan="2" style="width:11%;">TOTAL</th>
                    <th rowspan="2" style="width:11%;">KETERANGAN</th>
                </tr>
                <tr>
                    <th style="width:5%;">Hari</th>
                    <th style="width:9%;">Rate</th>
                    <th style="width:9%;">Total</th>
                    <th style="width:5%;">Jumlah</th>
                    <th style="width:9%;">Rate</th>
                    <th style="width:9%;">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($members as $idx => $member)
                    @php
                        $memberExpenses = $expenses->where('participant_name', $member);
                        $sharedExpenses = $expenses->whereNull('participant_name');

                        $uangMakan = $memberExpenses->firstWhere('kategori', 'uang_makan')
                            ?? $sharedExpenses->firstWhere('kategori', 'uang_makan');
                        $cuciPakaian = $memberExpenses->firstWhere('kategori', 'cuci_pakaian')
                            ?? $sharedExpenses->firstWhere('kategori', 'cuci_pakaian');
                        $transportasi = $memberExpenses->firstWhere('kategori', 'transportasi_lokal')
                            ?? $memberExpenses->firstWhere('kategori', 'transport')
                            ?? $sharedExpenses->firstWhere('kategori', 'transportasi_lokal')
                            ?? $sharedExpenses->firstWhere('kategori', 'transport');

                        $umHari = $uangMakan?->jumlah_hari;
                        $umRate = $uangMakan ? (float) $uangMakan->jumlah : 0;
                        $umTotal = $umRate;

                        $cpJumlah = $cuciPakaian?->jumlah_hari;
                        $cpRate = $cuciPakaian ? (float) $cuciPakaian->jumlah : 0;
                        $cpTotal = $cpRate;

                        $transportTotal = $transportasi ? (float) $transportasi->jumlah : 0;
                        $rowTotal = $umTotal + $cpTotal + $transportTotal;
                        $totalBiaya += $rowTotal;
                    @endphp
                    <tr>
                        <td class="center">{{ $idx + 1 }}</td>
                        <td class="left">{{ $member }}</td>
                        <td class="center">{{ $umHari ?: '' }}</td>
                        <td class="right">{{ $umRate > 0 ? number_format($umRate, 0, ',', '.') : '' }}</td>
                        <td class="right">{{ $umTotal > 0 ? number_format($umTotal, 0, ',', '.') : '' }}</td>
                        <td class="center">{{ $cpJumlah ?: '' }}</td>
                        <td class="right">{{ $cpRate > 0 ? number_format($cpRate, 0, ',', '.') : '' }}</td>
                        <td class="right">{{ $cpTotal > 0 ? number_format($cpTotal, 0, ',', '.') : '' }}</td>
                        <td class="right">{{ $transportTotal > 0 ? number_format($transportTotal, 0, ',', '.') : '' }}</td>
                        <td class="right">{{ $rowTotal > 0 ? number_format($rowTotal, 0, ',', '.') : '' }}</td>
                        <td></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="center">Belum ada rincian biaya.</td>
                    </tr>
                @endforelse
                <tr class="total-row">
                    <td colspan="9" class="right">TOTAL</td>
                    <td class="right">{{ number_format($totalBiaya, 0, ',', '.') }}</td>
                    <td></td>
                </tr>
            </tbody>
        </table>

        <table class="signatures">
            <tr>
                <td>
                    <div class="sign-role">Admin</div>
                    <div class="sign-space"></div>
                    <div class="sign-name">{{ $adminSigner }}</div>
                </td>
                <td>
                    <div class="sign-role">Manager</div>
                    <div class="sign-space"></div>
                    <div class="sign-name">{{ $managerSigner }}</div>
                </td>
                <td>
                    <div class="sign-role">Direksi</div>
                    <div class="sign-space"></div>
                    <div class="sign-name">{{ $direksiSigner }}</div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
