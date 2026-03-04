<?php

namespace App\Http\Requests\Sppd;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSppdRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tujuan' => ['sometimes','required','string','max:255'],
            'kota' => ['nullable','string','max:255'],
            'negara' => ['nullable','string','max:255'],
            'tanggal_berangkat' => ['sometimes','required','date'],
            'tanggal_pulang' => ['sometimes','required','date','after_or_equal:tanggal_berangkat'],
            'lama_hari' => ['sometimes','required','integer','min:1'],
            'maksud_perjalanan' => ['sometimes','required','string'],
            'status' => ['sometimes','required','string','in:draft,diajukan,disetujui,ditolak,selesai'],
            'alasan_penolakan' => ['nullable','string'],
            'siap_bayar' => ['nullable','boolean'],
        ];
    }
}

