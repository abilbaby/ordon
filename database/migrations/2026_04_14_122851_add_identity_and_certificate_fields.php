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
        Schema::table('donors', function (Blueprint $table) {
            $table->string('identity_type')->nullable()->after('family_contact');
            $table->string('identity_number')->nullable()->after('identity_type');
            $table->boolean('identity_verified')->default(false)->after('identity_number');
        });

        Schema::table('recipients', function (Blueprint $table) {
            $table->string('identity_type')->nullable()->after('medical_record_path');
            $table->string('identity_number')->nullable()->after('identity_type');
            $table->boolean('identity_verified')->default(false)->after('identity_number');
        });

        Schema::table('hospitals', function (Blueprint $table) {
            $table->string('identity_type')->nullable()->after('blacklisted');
            $table->string('identity_number')->nullable()->after('identity_type');
            $table->boolean('identity_verified')->default(false)->after('identity_number');
        });

        Schema::table('transplants', function (Blueprint $table) {
            $table->string('recipient_name_override')->nullable()->after('post_operation_report');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transplants', function (Blueprint $table) {
            $table->dropColumn('recipient_name_override');
        });

        Schema::table('hospitals', function (Blueprint $table) {
            $table->dropColumn(['identity_type', 'identity_number', 'identity_verified']);
        });

        Schema::table('recipients', function (Blueprint $table) {
            $table->dropColumn(['identity_type', 'identity_number', 'identity_verified']);
        });

        Schema::table('donors', function (Blueprint $table) {
            $table->dropColumn(['identity_type', 'identity_number', 'identity_verified']);
        });
    }
};
