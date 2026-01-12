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
        Schema::create('workload_allocations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->date('date');
            $table->decimal('allocated_hours', 5, 2)->default(0);
            $table->decimal('available_hours', 5, 2)->default(8);
            $table->decimal('utilization_rate', 5, 2)->default(0);
            $table->integer('assignments_count')->default(0);
            $table->timestamps();
            
            $table->unique(['user_id', 'date']);
            $table->index('user_id');
            $table->index('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workload_allocations');
    }
};
