<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('recipient_verifications', function (Blueprint $table) {
            if (! Schema::hasColumn('recipient_verifications', 'date_of_birth')) {
                $table->date('date_of_birth')->nullable()->after('recipient_name');
            }
            if (! Schema::hasColumn('recipient_verifications', 'gender')) {
                $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('date_of_birth');
            }
            if (! Schema::hasColumn('recipient_verifications', 'organ_needed')) {
                $table->string('organ_needed', 50)->nullable()->after('blood_group');
            }
            if (! Schema::hasColumn('recipient_verifications', 'urgency_level')) {
                $table->enum('urgency_level', ['high', 'medium', 'low'])->nullable()->after('organ_needed');
            }
            if (! Schema::hasColumn('recipient_verifications', 'medical_notes')) {
                $table->text('medical_notes')->nullable()->after('notes');
            }
            if (! Schema::hasColumn('recipient_verifications', 'contact_number')) {
                $table->string('contact_number', 20)->nullable()->after('phone');
            }
        });
    }

    public function down(): void
    {
        Schema::table('recipient_verifications', function (Blueprint $table) {
            foreach (['date_of_birth', 'gender', 'organ_needed', 'urgency_level', 'medical_notes', 'contact_number'] as $column) {
                if (Schema::hasColumn('recipient_verifications', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
