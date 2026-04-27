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
            $table->boolean('approved')->default(false)->after('user_id');
            $table->boolean('fraud_flag')->default(false)->after('approved');
            $table->boolean('blacklisted')->default(false)->after('fraud_flag');
            $table->string('region')->nullable()->after('organ_type');
            $table->text('medical_conditions')->nullable()->after('region');
            $table->string('donation_type')->default('living')->after('medical_conditions');
            $table->json('donation_preferences')->nullable()->after('donation_type');
            $table->boolean('consent_given')->default(false)->after('donation_preferences');
            $table->string('family_contact')->nullable()->after('consent_given');
            $table->boolean('pre_donation_checklist_completed')->default(false)->after('family_contact');
            $table->string('eligibility_status')->default('pending')->after('pre_donation_checklist_completed');
        });

        Schema::table('recipients', function (Blueprint $table) {
            $table->string('region')->nullable()->after('organ_needed');
            $table->json('organs_needed')->nullable()->after('region');
            $table->string('medical_record_path')->nullable()->after('organs_needed');
            $table->boolean('doctor_approved')->default(false)->after('medical_record_path');
            $table->boolean('priority_escalation_requested')->default(false)->after('doctor_approved');
            $table->boolean('is_emergency')->default(false)->after('priority_escalation_requested');
        });

        Schema::table('hospitals', function (Blueprint $table) {
            $table->boolean('fraud_flag')->default(false)->after('approved');
            $table->boolean('blacklisted')->default(false)->after('fraud_flag');
        });

        Schema::table('matches', function (Blueprint $table) {
            $table->boolean('admin_override')->default(false)->after('status');
            $table->text('override_reason')->nullable()->after('admin_override');
        });

        Schema::table('transplants', function (Blueprint $table) {
            $table->string('surgery_status')->default('Scheduled')->after('status');
            $table->string('transport_status')->default('Pending')->after('surgery_status');
            $table->text('post_operation_report')->nullable()->after('transport_status');
        });

        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospital_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('specialization');
            $table->string('phone')->nullable();
            $table->timestamps();
        });

        Schema::create('organ_inventories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospital_id')->constrained()->cascadeOnDelete();
            $table->string('organ_type');
            $table->unsignedInteger('units')->default(0);
            $table->timestamps();
        });

        Schema::create('emergency_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recipient_id')->constrained()->cascadeOnDelete();
            $table->string('organ_type');
            $table->string('blood_group');
            $table->string('status')->default('open');
            $table->foreignId('accepted_donor_id')->nullable()->constrained('donors')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action');
            $table->string('module');
            $table->text('details')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('emergency_requests');
        Schema::dropIfExists('organ_inventories');
        Schema::dropIfExists('doctors');

        Schema::table('transplants', function (Blueprint $table) {
            $table->dropColumn(['surgery_status', 'transport_status', 'post_operation_report']);
        });

        Schema::table('matches', function (Blueprint $table) {
            $table->dropColumn(['admin_override', 'override_reason']);
        });

        Schema::table('hospitals', function (Blueprint $table) {
            $table->dropColumn(['fraud_flag', 'blacklisted']);
        });

        Schema::table('recipients', function (Blueprint $table) {
            $table->dropColumn([
                'region',
                'organs_needed',
                'medical_record_path',
                'doctor_approved',
                'priority_escalation_requested',
                'is_emergency',
            ]);
        });

        Schema::table('donors', function (Blueprint $table) {
            $table->dropColumn([
                'approved',
                'fraud_flag',
                'blacklisted',
                'region',
                'medical_conditions',
                'donation_type',
                'donation_preferences',
                'consent_given',
                'family_contact',
                'pre_donation_checklist_completed',
                'eligibility_status',
            ]);
        });
    }
};
