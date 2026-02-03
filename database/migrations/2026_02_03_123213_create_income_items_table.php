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
        Schema::create('income_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_section_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('rental_property_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('description');
            $table->decimal('amount', 15, 2);
            $table->string('quarter')->nullable(); // all, q1, q2, q3, q4
            $table->text('client_comment')->nullable();
            $table->text('own_comment')->nullable();
            $table->timestamps();

            $table->index('work_section_id');
            $table->index('rental_property_id');
            $table->index('quarter');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('income_items');
    }
};
