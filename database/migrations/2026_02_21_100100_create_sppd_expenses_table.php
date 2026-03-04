<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sppd_expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sppd_id')->constrained('sppd_requests')->cascadeOnDelete();
            $table->string('kategori');
            $table->string('deskripsi')->nullable();
            $table->decimal('jumlah', 12, 2);
            $table->string('mata_uang', 10)->default('IDR');
            $table->date('tanggal');
            $table->timestamps();

            $table->index('sppd_id');
            $table->index('tanggal');
            $table->index('kategori');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sppd_expenses');
    }
};

