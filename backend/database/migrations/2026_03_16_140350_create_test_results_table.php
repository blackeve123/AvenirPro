<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('test_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            // JSON object storing scores per RIASEC type: {"R":3,"I":12,"A":8,"S":6,"E":2,"C":1}
            $table->json('riasec_scores');
            // Top type(s) determined after calculation, e.g. "I" or "IA"
            $table->string('top_profile', 10)->nullable();
            // JSON array of recommended job ids with match percentages
            $table->json('recommended_jobs')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('test_results');
    }
};
