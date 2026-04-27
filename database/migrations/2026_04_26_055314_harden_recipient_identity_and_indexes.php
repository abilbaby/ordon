<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('recipients', function (Blueprint $table): void {
            if (! Schema::hasColumn('recipients', 'identity_number_hash')) {
                $table->string('identity_number_hash', 64)->nullable()->after('identity_number');
            }
            if (! Schema::hasColumn('recipients', 'identity_number_last4')) {
                $table->string('identity_number_last4', 4)->nullable()->after('identity_number_hash');
            }
            $table->index(['status', 'created_at'], 'recipients_status_created_idx');
            $table->index(['hospital_id', 'status'], 'recipients_hospital_status_idx');
        });

        DB::table('recipients')
            ->select(['id', 'identity_number'])
            ->orderBy('id')
            ->get()
            ->each(function ($recipient): void {
                if (! $recipient->identity_number) {
                    return;
                }

                $normalized = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', (string) $recipient->identity_number));
                if ($normalized === '') {
                    return;
                }

                DB::table('recipients')
                    ->where('id', $recipient->id)
                    ->update([
                        'identity_number_hash' => hash('sha256', $normalized),
                        'identity_number_last4' => substr($normalized, -4),
                    ]);
            });

        Schema::table('recipients', function (Blueprint $table): void {
            $table->unique('identity_number_hash', 'recipients_identity_number_hash_unique');
        });

        Schema::table('matches', function (Blueprint $table): void {
            $table->index(['status', 'created_at'], 'matches_status_created_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('matches', function (Blueprint $table): void {
            $table->dropIndex('matches_status_created_idx');
        });

        Schema::table('recipients', function (Blueprint $table): void {
            $table->dropUnique('recipients_identity_number_hash_unique');
            $table->dropIndex('recipients_status_created_idx');
            $table->dropIndex('recipients_hospital_status_idx');
            $table->dropColumn(['identity_number_hash', 'identity_number_last4']);
        });
    }
};
