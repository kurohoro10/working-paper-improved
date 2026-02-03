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
        Schema::create('working_papers', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number')->unique(); // Auto-generated: WP-2024-00001
            $table->foreignId('client_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->string('financial_year', 10); // e.g., "2023-2024"
            $table->json('selected_work_types'); // Array of work types enabled
            $table->string('status')->default('draft'); // draft, in_progress, completed, archived
            $table->text('notes')->nullable();
            $table->string('access_token', 64)->unique()->nullable(); // For guest access
            $table->timestamp('token_expires_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('reference_number');
            $table->index('client_id');
            $table->index('created_by');
            $table->index('status');
            $table->index('access_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('working_papers');
    }
};
