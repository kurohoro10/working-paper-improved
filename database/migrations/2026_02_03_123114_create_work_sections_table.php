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
        Schema::create('work_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('working_paper_id')->constrained()->onDelete('cascade');
            $table->string('work_type'); // wage, rental_property, sole_trader, bas, ctax, ttax, smsf
            $table->json('data')->nullable(); // Flexible JSON storage for type-specific data
            $table->timestamps();

            $table->index(['working_paper_id', 'work_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_sections');
    }
};
