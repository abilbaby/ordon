<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recipient_verifications', function (Blueprint $table): void {
            $table->id();
            $table->string('rvid')->unique();
            $table->foreignId('hospital_id')->constrained()->cascadeOnDelete();
            $table->string('recipient_name');
            $table->string('email');
            $table->string('phone');
            $table->string('blood_group');
            $table->text('notes')->nullable();
            $table->string('registration_link')->nullable();
            $table->string('status')->default('Pending');
            $table->timestamp('expires_at');
            $table->timestamps();
        });

        Schema::table('recipients', function (Blueprint $table): void {
            $table->foreignId('hospital_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
            $table->foreignId('recipient_verification_id')->nullable()->after('hospital_id')->constrained('recipient_verifications')->nullOnDelete();
            $table->boolean('hospital_verified')->default(false)->after('doctor_approved');
            $table->boolean('admin_approved')->default(false)->after('hospital_verified');
            $table->boolean('flagged_for_review')->default(false)->after('admin_approved');
        });

        DB::table('recipients')
            ->where('identity_verified', true)
            ->whereIn('status', ['VERIFIED', 'MATCHED', 'APPROVED', 'COMPLETED'])
            ->update([
                'hospital_verified' => true,
                'admin_approved' => true,
            ]);
    }

    public function down(): void
    {
        Schema::table('recipients', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('recipient_verification_id');
            $table->dropConstrainedForeignId('hospital_id');
            $table->dropColumn(['hospital_verified', 'admin_approved', 'flagged_for_review']);
        });

        Schema::dropIfExists('recipient_verifications');
    }
};
