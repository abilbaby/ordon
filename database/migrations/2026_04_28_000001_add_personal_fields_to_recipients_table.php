<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('recipients', function (Blueprint $table) {
            if (! Schema::hasColumn('recipients', 'phone')) {
                $table->string('phone', 20)->nullable()->after('user_id');
            }
            if (! Schema::hasColumn('recipients', 'address')) {
                $table->string('address', 255)->nullable()->after('phone');
            }
            if (! Schema::hasColumn('recipients', 'date_of_birth')) {
                $table->date('date_of_birth')->nullable()->after('address');
            }
            if (! Schema::hasColumn('recipients', 'gender')) {
                $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('date_of_birth');
            }
            if (! Schema::hasColumn('recipients', 'emergency_contact_name')) {
                $table->string('emergency_contact_name', 100)->nullable()->after('gender');
            }
            if (! Schema::hasColumn('recipients', 'emergency_contact_phone')) {
                $table->string('emergency_contact_phone', 20)->nullable()->after('emergency_contact_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('recipients', function (Blueprint $table) {
            foreach (['phone', 'address', 'date_of_birth', 'gender', 'emergency_contact_name', 'emergency_contact_phone'] as $column) {
                if (Schema::hasColumn('recipients', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
