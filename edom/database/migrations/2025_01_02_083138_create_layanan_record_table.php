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
        Schema::create('layanan_record', function (Blueprint $table) {
            $table->string('response_id');
            $table->string('user_name');
            $table->string('student_id');
            $table->string('group_name');
            $table->string('question_text');
            $table->string('response_value');
            $table->string('year_semester');
            $table->timestamp('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('layanan_record');
    }
};
