<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sppd_requests', function (Blueprint $table) {
            $table->longText('sumber_anggaran')->nullable()->after('jenis_perjalanan');
        });
    }

    public function down(): void
    {
        Schema::table('sppd_requests', function (Blueprint $table) {
            $table->dropColumn('sumber_anggaran');
        });
    }
};
