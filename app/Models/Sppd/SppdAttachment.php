<?php

namespace App\Models\Sppd;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SppdAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'sppd_id',
        'jenis',
        'path',
        'ukuran',
        'mime',
        'uploaded_by',
        'uploaded_at',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
        'ukuran' => 'integer',
    ];

    public function sppd()
    {
        return $this->belongsTo(SppdRequest::class, 'sppd_id');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}

