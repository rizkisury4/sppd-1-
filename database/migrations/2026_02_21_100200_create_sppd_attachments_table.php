<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sppd_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sppd_id')->constrained('sppd_requests')->cascadeOnDelete();
            $table->string('jenis');
            $table->string('path');
            $table->unsignedBigInteger('ukuran')->nullable();
            $table->string('mime')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('uploaded_at')->nullable();
            $table->timestamps();

            $table->index('sppd_id');
            $table->index('jenis');
            $table->index('uploaded_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sppd_attachments');
    }
};

