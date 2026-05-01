<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('nip')->unique();
            $table->string('name');
            $table->string('position');
            $table->string('employment_status');
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->index('name');
            $table->index('employment_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};