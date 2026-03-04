<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('travel_categories', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::table('sppd_expenses', function (Blueprint $table) {
            $table->foreignId('travel_category_id')->nullable()->constrained('travel_categories')->nullOnDelete();
            $table->index('travel_category_id');
        });
    }

    public function down(): void
    {
        Schema::table('sppd_expenses', function (Blueprint $table) {
            $table->dropConstrainedForeignId('travel_category_id');
        });
        Schema::dropIfExists('travel_categories');
    }
};

