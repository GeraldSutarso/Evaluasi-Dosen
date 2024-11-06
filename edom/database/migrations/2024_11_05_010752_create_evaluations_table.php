<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // User who submitted the evaluation
            $table->foreignId('matkul_id')->constrained()->onDelete('cascade'); // Matkul evaluated
            $table->foreignId('lecturer_id')->constrained()->onDelete('cascade'); // Lecturer being evaluated
            $table->boolean('completed')->default(0);
            $table->integer('week_number');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluations');
    }
};
