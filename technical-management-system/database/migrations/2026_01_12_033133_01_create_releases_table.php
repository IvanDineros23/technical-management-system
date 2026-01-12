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
        Schema::create('releases', function (Blueprint $table) {
            $table->id();
            $table->string('release_number', 50)->unique();
            $table->foreignId('job_order_id')->constrained('job_orders')->onDelete('restrict');
            $table->unsignedBigInteger('released_by');
            $table->string('released_to');
            $table->date('release_date');
            $table->time('release_time')->nullable();
            $table->enum('delivery_method', ['pickup', 'courier', 'email', 'hand_carry'])->nullable();
            $table->string('tracking_number')->nullable();
            $table->string('recipient_name')->nullable();
            $table->string('recipient_signature')->nullable()->comment('signature path');
            $table->string('status', 50)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('release_number');
            $table->index('job_order_id');
            $table->index('release_date');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('releases');
    }
};
