<?php

namespace App\Listeners;

use App\Events\SppdApproved;
use App\Events\SppdRejected;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendSppdNotification implements ShouldQueue
{
    public function handle(object $event): void
    {
        if ($event instanceof SppdApproved) {
            $sppd = $event->sppd;
            Log::info('SPPD disetujui', ['kode' => $sppd->kode, 'pegawai' => $sppd->pegawai_id]);
            $this->mail($sppd->pegawai->email, 'SPPD Disetujui', "Pengajuan {$sppd->kode} telah disetujui.");
        }

        if ($event instanceof SppdRejected) {
            $sppd = $event->sppd;
            Log::info('SPPD ditolak', ['kode' => $sppd->kode, 'pegawai' => $sppd->pegawai_id, 'alasan' => $event->reason]);
            $this->mail($sppd->pegawai->email, 'SPPD Ditolak', "Pengajuan {$sppd->kode} ditolak. Alasan: {$event->reason}");
        }
    }

    protected function mail(string $to, string $subject, string $body): void
    {
        try {
            Mail::raw($body, function ($message) use ($to, $subject) {
                $message->to($to)->subject($subject);
            });
        } catch (\Throwable $e) {
            Log::warning('Gagal mengirim email notifikasi SPPD', ['error' => $e->getMessage()]);
        }
    }
}

