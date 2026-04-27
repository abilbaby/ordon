<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('donation_histories', function (Blueprint $table): void {
            $table->unique(['donor_id', 'organ_type'], 'donation_histories_donor_organ_unique');
        });

        Schema::table('matches', function (Blueprint $table): void {
            $table->unsignedTinyInteger('match_score')->nullable()->after('score');
            $table->text('reason')->nullable()->after('match_score');
            $table->softDeletes();
        });

        Schema::create('status_histories', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('entity_id');
            $table->string('entity_type');
            $table->string('old_status')->nullable();
            $table->string('new_status');
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('changed_at');
            $table->timestamps();
            $table->index(['entity_type', 'entity_id']);
        });

        Schema::create('user_notifications', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->string('title');
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->string('entity_type')->nullable();
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'is_read']);
            $table->index(['type', 'created_at']);
        });

        Schema::table('transplants', function (Blueprint $table): void {
            $table->string('certificate_id')->nullable()->unique()->after('recipient_name_override');
        });

        Schema::table('donors', function (Blueprint $table): void {
            $table->softDeletes();
        });

        Schema::table('recipients', function (Blueprint $table): void {
            $table->softDeletes();
        });

        Schema::table('hospitals', function (Blueprint $table): void {
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('hospitals', function (Blueprint $table): void {
            $table->dropSoftDeletes();
        });

        Schema::table('recipients', function (Blueprint $table): void {
            $table->dropSoftDeletes();
        });

        Schema::table('donors', function (Blueprint $table): void {
            $table->dropSoftDeletes();
        });

        Schema::table('transplants', function (Blueprint $table): void {
            $table->dropUnique(['certificate_id']);
            $table->dropColumn('certificate_id');
        });

        Schema::dropIfExists('user_notifications');
        Schema::dropIfExists('status_histories');

        Schema::table('matches', function (Blueprint $table): void {
            $table->dropSoftDeletes();
            $table->dropColumn(['match_score', 'reason']);
        });

        Schema::table('donation_histories', function (Blueprint $table): void {
            $table->dropUnique('donation_histories_donor_organ_unique');
        });
    }
};
