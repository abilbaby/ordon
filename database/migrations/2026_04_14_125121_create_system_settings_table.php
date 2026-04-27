<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('urgency_weight')->default(40);
            $table->unsignedTinyInteger('waiting_weight')->default(30);
            $table->unsignedTinyInteger('compatibility_weight')->default(20);
            $table->unsignedInteger('emergency_threshold')->default(180);
            $table->unsignedTinyInteger('max_daily_surgeries')->default(6);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};
