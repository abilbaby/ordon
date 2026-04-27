<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('donors', function (Blueprint $table): void {
            $table->string('notes')->nullable()->after('family_contact');
        });

        Schema::create('donor_organs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('donor_id')->constrained()->cascadeOnDelete();
            $table->string('organ_type');
            $table->timestamps();
            $table->unique(['donor_id', 'organ_type']);
        });

        DB::table('donors')
            ->select(['id', 'organ_type'])
            ->orderBy('id')
            ->get()
            ->each(function ($donor): void {
                if (! empty($donor->organ_type)) {
                    DB::table('donor_organs')->insert([
                        'donor_id' => $donor->id,
                        'organ_type' => $donor->organ_type,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            });

        Schema::create('donation_histories', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('donor_id')->constrained()->cascadeOnDelete();
            $table->foreignId('recipient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('hospital_id')->constrained()->cascadeOnDelete();
            $table->string('organ_type');
            $table->date('donation_date');
            $table->string('status')->default('Scheduled');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donation_histories');
        Schema::dropIfExists('donor_organs');

        Schema::table('donors', function (Blueprint $table): void {
            $table->dropColumn('notes');
        });
    }
};
