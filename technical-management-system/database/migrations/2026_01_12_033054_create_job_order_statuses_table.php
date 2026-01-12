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
        Schema::create('job_order_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_order_id')->constrained('job_orders')->onDelete('cascade');
            $table->string('status', 50);
            $table->string('previous_status', 50)->nullable();
            $table->unsignedBigInteger('changed_by');
            $table->timestamp('changed_at')->useCurrent();
            $table->text('remarks')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->index('job_order_id');
            $table->index('status');
            $table->index('changed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_order_statuses');
    }
};
