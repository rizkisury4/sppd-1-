<?php

namespace App\Http\Requests\Sppd;

use Closure;
use Illuminate\Foundation\Http\FormRequest;

class StoreExpenseRequest extends FormRequest
{
    public const UANG_MAKAN_RATES = [300000, 275000, 250000, 225000];

    public const CUCI_PAKAIAN_FLAT_RATE = 25000;

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->input('kategori') === 'cuci_pakaian') {
            $this->merge([
                'jumlah' => self::CUCI_PAKAIAN_FLAT_RATE,
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'kategori' => ['required','string','in:transport,akomodasi,harian,lainnya,uang_makan,cuci_pakaian'],
            'participant_name' => ['required','string','max:255'],
            'deskripsi' => ['nullable','string','max:255'],
            'jumlah' => [
                'required',
                'numeric',
                'min:0',
                function (string $attribute, mixed $value, Closure $fail): void {
                    $rate = (int) round((float) $value);

                    if ($this->input('kategori') === 'cuci_pakaian' && $rate !== self::CUCI_PAKAIAN_FLAT_RATE) {
                        $fail('Rate cuci pakaian harus flat 25.000.');
                    }
                },
            ],
            'jumlah_hari' => ['required','integer','min:1'],
            'mata_uang' => ['nullable','string','max:10'],
            'tanggal' => ['required','date'],
        ];
    }
}
