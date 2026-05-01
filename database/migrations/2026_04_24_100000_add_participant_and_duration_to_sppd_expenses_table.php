<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sppd_expenses', function (Blueprint $table) {
            $table->string('participant_name')->nullable()->after('kategori');
            $table->unsignedInteger('jumlah_hari')->default(1)->after('jumlah');
            $table->index('participant_name');
        });
    }

    public function down(): void
    {
        Schema::table('sppd_expenses', function (Blueprint $table) {
            $table->dropIndex(['participant_name']);
            $table->dropColumn(['participant_name', 'jumlah_hari']);
        });
    }
};