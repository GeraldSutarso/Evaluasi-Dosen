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
        Schema::create('response_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('evaluation_id');
            $table->string('user_name');
            $table->string('group_name');
            $table->string('lecturer_name');
            $table->string('matkul_name');
            $table->string('question_text');
            $table->integer('response_value');
            $table->string('year_semester'); // e.g., "2023_I" or "2023_II"
            $table->timestamps();

            // Add an index on evaluation_id for faster lookups
            $table->index('evaluation_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('response_records');
    }
};
