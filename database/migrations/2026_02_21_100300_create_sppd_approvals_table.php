<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sppd_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sppd_id')->constrained('sppd_requests')->cascadeOnDelete();
            $table->foreignId('approver_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status');
            $table->text('catatan')->nullable();
            $table->timestamp('acted_at')->nullable();
            $table->timestamps();

            $table->index('sppd_id');
            $table->index('approver_id');
            $table->index('status');
            $table->index('acted_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sppd_approvals');
    }
};

