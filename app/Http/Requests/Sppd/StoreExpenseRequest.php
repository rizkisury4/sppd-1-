<?php

namespace App\Http\Requests\Sppd;

use Illuminate\Foundation\Http\FormRequest;

class StoreExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kategori' => ['required','string','in:transport,akomodasi,harian,lainnya,uang_makan,cuci_pakaian'],
            'participant_name' => ['required','string','max:255'],
            'deskripsi' => ['nullable','string','max:255'],
            'jumlah' => ['required','numeric','min:0'],
            'jumlah_hari' => ['required','integer','min:1'],
            'mata_uang' => ['nullable','string','max:10'],
            'tanggal' => ['required','date'],
        ];
    }
}
