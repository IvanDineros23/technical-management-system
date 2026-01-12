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
        Schema::create('equipment_maintenance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_id')->constrained('equipment')->onDelete('restrict');
            $table->enum('maintenance_type', ['preventive', 'corrective', 'calibration', 'repair'])->nullable();
            $table->unsignedBigInteger('performed_by')->nullable();
            $table->timestamp('performed_at');
            $table->text('description')->nullable();
            $table->text('parts_replaced')->nullable();
            $table->decimal('cost', 10, 2)->nullable();
            $table->integer('downtime_hours')->nullable();
            $table->date('next_maintenance_date')->nullable();
            $table->string('status', 50)->nullable();
            $table->timestamps();
            
            $table->index('equipment_id');
            $table->index('maintenance_type');
            $table->index('performed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment_maintenance');
    }
};
