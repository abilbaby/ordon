<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('recipient_verifications', function (Blueprint $table) {
            $table->unsignedInteger('waiting_time')->nullable()->after('urgency_level');
            $table->string('other_organs_needed')->nullable()->after('waiting_time');
        });
    }

    public function down(): void
    {
        Schema::table('recipient_verifications', function (Blueprint $table) {
            $table->dropColumn(['waiting_time', 'other_organs_needed']);
        });
    }
};
