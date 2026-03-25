<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('job_categories')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('sector')->nullable();
            $table->string('salary_range')->nullable();
            // RIASEC types this job is compatible with (comma-separated, e.g. "I,A,S")
            $table->string('riasec_types')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};
