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
            'deskripsi' => ['nullable','string','max:255'],
            'jumlah' => ['required','numeric','min:0'],
            'mata_uang' => ['nullable','string','max:10'],
            'tanggal' => ['required','date'],
        ];
    }
}
