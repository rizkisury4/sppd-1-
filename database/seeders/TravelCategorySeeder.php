<?php

namespace Database\Seeders;

use App\Models\TravelCategory;
use Illuminate\Database\Seeder;

class TravelCategorySeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['code' => 'TRN', 'name' => 'Transport', 'active' => true],
            ['code' => 'AKM', 'name' => 'Akomodasi', 'active' => true],
            ['code' => 'HRN', 'name' => 'Harian', 'active' => true],
            ['code' => 'OTH', 'name' => 'Lainnya', 'active' => true],
        ];
        foreach ($data as $row) {
            TravelCategory::firstOrCreate(['code' => $row['code']], $row);
        }
    }
}

