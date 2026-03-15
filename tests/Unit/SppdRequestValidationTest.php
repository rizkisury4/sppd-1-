<?php

namespace Tests\Unit;

use App\Http\Requests\Sppd\StoreExpenseRequest;
use App\Http\Requests\Sppd\StoreSppdRequest;
use App\Http\Requests\Sppd\UpdateSppdRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class SppdRequestValidationTest extends TestCase
{
    public function test_store_sppd_request_requires_core_fields(): void
    {
        $request = new StoreSppdRequest();
        $validator = Validator::make([], $request->rules());
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('tujuan', $validator->errors()->toArray());
        $this->assertArrayHasKey('tanggal_berangkat', $validator->errors()->toArray());
        $this->assertArrayHasKey('tanggal_pulang', $validator->errors()->toArray());
        $this->assertArrayHasKey('lama_hari', $validator->errors()->toArray());
        $this->assertArrayHasKey('maksud_perjalanan', $validator->errors()->toArray());
    }

    public function test_store_sppd_request_accepts_valid_payload(): void
    {
        $data = [
            'tujuan' => 'Jakarta',
            'kota' => 'Jakarta',
            'negara' => 'ID',
            'tanggal_berangkat' => now()->toDateString(),
            'tanggal_pulang' => now()->addDays(2)->toDateString(),
            'lama_hari' => 2,
            'maksud_perjalanan' => 'Rapat',
            'status' => 'diajukan',
        ];

        $request = new StoreSppdRequest();
        $validator = Validator::make($data, $request->rules());
        $this->assertFalse($validator->fails(), json_encode($validator->errors()->toArray()));
    }

    public function test_store_expense_requires_correct_enum_and_amount(): void
    {
        $bad = [
            'kategori' => 'wrong',
            'jumlah' => -5,
            'tanggal' => 'not-a-date',
        ];
        $req = new StoreExpenseRequest();
        $v = Validator::make($bad, $req->rules());
        $this->assertTrue($v->fails());

        $good = [
            'kategori' => 'transport',
            'deskripsi' => 'Taksi',
            'jumlah' => 150000,
            'mata_uang' => 'IDR',
            'tanggal' => now()->toDateString(),
        ];
        $v2 = Validator::make($good, $req->rules());
        $this->assertFalse($v2->fails(), json_encode($v2->errors()->toArray()));
    }

    public function test_update_sppd_request_sometimes_rules(): void
    {
        $req = new UpdateSppdRequest();
        // Empty data is allowed for update (no change)
        $v1 = Validator::make([], $req->rules());
        $this->assertFalse($v1->fails());

        // If provided, fields must be valid
        $bad = ['status' => 'invalid'];
        $v2 = Validator::make($bad, $req->rules());
        $this->assertTrue($v2->fails());

        $good = ['status' => 'draft'];
        $v3 = Validator::make($good, $req->rules());
        $this->assertFalse($v3->fails());
    }
}

