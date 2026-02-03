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
        Schema::create('rental_properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_section_id')->constrained()->onDelete('cascade');
            $table->string('address_label'); // Nickname or address
            $table->string('full_address')->nullable();
            $table->decimal('ownership_percentage', 5, 2)->nullable(); // e.g., 50.00 for 50%
            $table->date('period_rented_from')->nullable();
            $table->date('period_rented_to')->nullable();
            $table->timestamps();

            $table->index('work_section_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rental_properties');
    }
};
