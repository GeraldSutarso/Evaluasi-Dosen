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
        Schema::create('matkuls', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('lecturer_id')->constrained('lecturers')->onDelete('cascade'); // Links to lecturers
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matkuls');
    }
};
