<?php

namespace App\Http\Requests\Sppd;

use Illuminate\Foundation\Http\FormRequest;

class StoreSppdRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kode' => ['nullable','string','max:50'],
            'tujuan' => ['required','string','max:255'],
            'kota' => ['nullable','string','max:255'],
            'negara' => ['nullable','string','max:255'],
            'jenis_perjalanan' => ['required','string','in:diklat,non_diklat'],
            'sumber_anggaran' => ['nullable','string'],
            'pejabat_perintah_id' => ['nullable','integer','exists:users,id'],
            'tanggal_berangkat' => ['required','date'],
            'tanggal_pulang' => ['required','date','after_or_equal:tanggal_berangkat'],
            'lama_hari' => ['required','integer','min:1'],
            'maksud_perjalanan' => ['required','string'],
            'status' => ['nullable','string','in:draft,diajukan,disetujui,ditolak,selesai'],
        ];
    }
}
