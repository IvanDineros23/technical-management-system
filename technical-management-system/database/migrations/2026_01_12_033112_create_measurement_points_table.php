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
        Schema::create('measurement_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calibration_id')->constrained('calibrations')->onDelete('cascade');
            $table->integer('point_number');
            $table->decimal('reference_value', 15, 6);
            $table->decimal('uut_reading', 15, 6);
            $table->decimal('error', 15, 6)->nullable();
            $table->decimal('uncertainty', 15, 6)->nullable();
            $table->string('acceptance_criteria')->nullable();
            $table->enum('status', ['pass', 'fail'])->nullable();
            $table->json('readings_ascending')->nullable();
            $table->json('readings_descending')->nullable();
            $table->timestamps();
            
            $table->index('calibration_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('measurement_points');
    }
};
