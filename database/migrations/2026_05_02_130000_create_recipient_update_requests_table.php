<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recipient_update_requests', function (Blueprint $table) {
            $table->id();
            
            // Foreign Keys
            $table->foreignId('recipient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('hospital_id')->constrained()->cascadeOnDelete();
            $table->foreignId('requested_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            
            // Identity Fields (Approval Required)
            $table->string('requested_full_name')->nullable();
            $table->date('requested_dob')->nullable();
            $table->string('requested_gender')->nullable();
            
            // Medical Fields (Approval Required)
            $table->string('requested_blood_group')->nullable();
            $table->string('requested_organ_needed')->nullable();
            $table->string('requested_urgency_level')->nullable();
            $table->unsignedInteger('requested_waiting_time')->nullable();
            $table->json('requested_other_organs')->nullable();
            
            // Metadata
            $table->text('reason');
            $table->enum('status', ['Pending', 'Approved', 'Rejected'])->default('Pending');
            $table->json('approved_fields')->nullable();
            $table->text('reviewer_note')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['recipient_id', 'status']);
            $table->index(['hospital_id', 'status']);
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recipient_update_requests');
    }
};
