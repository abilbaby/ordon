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
        Schema::table('transplants', function (Blueprint $table) {
            $table->date('slot_date')->nullable()->after('scheduled_at');
            $table->string('slot_period')->nullable()->after('slot_date');
            $table->string('operating_room')->nullable()->after('slot_period');
            $table->string('surgeon_name')->nullable()->after('operating_room');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transplants', function (Blueprint $table) {
            $table->dropColumn(['slot_date', 'slot_period', 'operating_room', 'surgeon_name']);
        });
    }
};
