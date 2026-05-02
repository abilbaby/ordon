<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transplants', function (Blueprint $table) {
            if (! Schema::hasColumn('transplants', 'doctor_id')) {
                $table->foreignId('doctor_id')->nullable()->after('match_id')->constrained('doctors')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('transplants', function (Blueprint $table) {
            if (Schema::hasColumn('transplants', 'doctor_id')) {
                $table->dropConstrainedForeignId('doctor_id');
            }
        });
    }
};
