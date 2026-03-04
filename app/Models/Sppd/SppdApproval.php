<?php

namespace App\Models\Sppd;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SppdApproval extends Model
{
    use HasFactory;

    protected $fillable = [
        'sppd_id',
        'approver_id',
        'status',
        'catatan',
        'acted_at',
    ];

    protected $casts = [
        'acted_at' => 'datetime',
    ];

    public function sppd()
    {
        return $this->belongsTo(SppdRequest::class, 'sppd_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }
}

