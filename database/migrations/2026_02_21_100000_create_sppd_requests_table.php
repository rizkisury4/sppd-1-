<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sppd_requests', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->foreignId('pegawai_id')->constrained('users')->cascadeOnDelete();
            $table->string('tujuan');
            $table->string('kota')->nullable();
            $table->string('negara')->nullable();
            $table->date('tanggal_berangkat');
            $table->date('tanggal_pulang');
            $table->unsignedInteger('lama_hari');
            $table->text('maksud_perjalanan');
            $table->string('status')->index();
            $table->text('alasan_penolakan')->nullable();
            $table->foreignId('disetujui_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('disetujui_pada')->nullable();
            $table->boolean('siap_bayar')->default(false);
            $table->timestamps();

            $table->index('pegawai_id');
            $table->index('tanggal_berangkat');
            $table->index('tanggal_pulang');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sppd_requests');
    }
};

