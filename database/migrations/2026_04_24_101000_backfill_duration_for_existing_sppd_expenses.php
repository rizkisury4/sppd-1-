<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $rows = DB::table('sppd_expenses')
            ->join('sppd_requests', 'sppd_requests.id', '=', 'sppd_expenses.sppd_id')
            ->where('sppd_expenses.jumlah_hari', 1)
            ->get([
                'sppd_expenses.id as expense_id',
                'sppd_requests.lama_hari as lama_hari',
            ]);

        foreach ($rows as $row) {
            DB::table('sppd_expenses')
                ->where('id', $row->expense_id)
                ->update([
                    'jumlah_hari' => max((int) $row->lama_hari, 1),
                ]);
        }
    }

    public function down(): void
    {
        // No-op: previous duration values cannot be restored safely.
    }
};