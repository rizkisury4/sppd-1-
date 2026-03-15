<?php

namespace App\Models\Sppd;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SppdRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode',
        'pegawai_id',
        'department_id',
        'tujuan',
        'kota',
        'negara',
        'jenis_perjalanan',
        'sumber_anggaran',
        'pejabat_perintah_id',
        'tanggal_berangkat',
        'tanggal_pulang',
        'lama_hari',
        'maksud_perjalanan',
        'status',
        'alasan_penolakan',
        'disetujui_oleh',
        'disetujui_pada',
        'siap_bayar',
    ];

    protected $casts = [
        'tanggal_berangkat' => 'date',
        'tanggal_pulang' => 'date',
        'disetujui_pada' => 'datetime',
        'siap_bayar' => 'boolean',
        'lama_hari' => 'integer',
    ];

    public function pegawai()
    {
        return $this->belongsTo(User::class, 'pegawai_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }

    public function pejabatPerintah()
    {
        return $this->belongsTo(User::class, 'pejabat_perintah_id');
    }

    public function department()
    {
        return $this->belongsTo(\App\Models\Department::class);
    }

    public function expenses()
    {
        return $this->hasMany(SppdExpense::class, 'sppd_id');
    }

    public function attachments()
    {
        return $this->hasMany(SppdAttachment::class, 'sppd_id');
    }

    public function approvals()
    {
        return $this->hasMany(SppdApproval::class, 'sppd_id');
    }
}
