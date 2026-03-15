<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['code' => 'HR', 'name' => 'Human Resources', 'active' => true],
            ['code' => 'FIN', 'name' => 'Finance', 'active' => true],
            ['code' => 'OPS', 'name' => 'Operations', 'active' => true],
            ['code' => 'IT', 'name' => 'Information Technology', 'active' => true],
        ];
        foreach ($data as $row) {
            Department::firstOrCreate(['code' => $row['code']], $row);
        }
    }
}

