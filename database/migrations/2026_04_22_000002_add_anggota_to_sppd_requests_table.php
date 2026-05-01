<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sppd_requests', function (Blueprint $table) {
            $table->json('anggota')->nullable()->after('pegawai_id');
        });
    }

    public function down(): void
    {
        Schema::table('sppd_requests', function (Blueprint $table) {
            $table->dropColumn('anggota');
        });
    }
};
