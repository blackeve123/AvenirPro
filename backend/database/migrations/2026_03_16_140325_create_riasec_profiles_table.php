<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riasec_profiles', function (Blueprint $table) {
            $table->id();
            // R, I, A, S, E, C
            $table->char('code', 1)->unique();
            $table->string('name');           // e.g. "Realistic"
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riasec_profiles');
    }
};
