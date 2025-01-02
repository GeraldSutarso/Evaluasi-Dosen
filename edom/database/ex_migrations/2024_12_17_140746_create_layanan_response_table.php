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
        Schema::create('layanan_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            // $table->string('name');
            // $table->string('student_id'); Sorry brother, maybe next time 😣👈🤛🤏✌
            // $table->string('group_name');
            $table->foreignId('question_id')->constrained('layanan_questions');
            $table->string('response_value'); // Adjust type based on response type (e.g., rating scale)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('layanan_responses');
    }
};
