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
        Schema::create('expense_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_section_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('rental_property_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('description');
            $table->string('field_type')->nullable(); // A, B, C - required for certain work types
            $table->string('quarter')->nullable(); // all, q1, q2, q3, q4
            $table->decimal('amount_inc_gst', 15, 2)->nullable();
            $table->decimal('gst_amount', 15, 2)->nullable();
            $table->decimal('net_ex_gst', 15, 2)->nullable();
            $table->boolean('is_gst_free')->default(false);
            $table->text('client_comment')->nullable();
            $table->text('own_comment')->nullable();
            $table->timestamps();

            $table->index('work_section_id');
            $table->index('rental_property_id');
            $table->index('quarter');
            $table->index('field_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expense_items');
    }
};
