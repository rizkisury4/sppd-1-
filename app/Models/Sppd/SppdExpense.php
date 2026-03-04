<?php

namespace App\Models\Sppd;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SppdExpense extends Model
{
    use HasFactory;

    protected $fillable = [
        'sppd_id',
        'kategori',
        'deskripsi',
        'jumlah',
        'mata_uang',
        'tanggal',
        'travel_category_id',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'tanggal' => 'date',
    ];

    public function sppd()
    {
        return $this->belongsTo(SppdRequest::class, 'sppd_id');
    }

    public function travelCategory()
    {
        return $this->belongsTo(\App\Models\TravelCategory::class);
    }
}
