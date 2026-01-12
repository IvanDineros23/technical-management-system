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
        Schema::create('calibration_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calibration_id')->constrained('calibrations')->onDelete('cascade');
            $table->integer('measurement_number');
            $table->string('measurement_point', 100)->nullable();
            $table->decimal('nominal_value', 15, 6)->nullable();
            $table->decimal('measured_value', 15, 6)->nullable();
            $table->decimal('error', 15, 6)->nullable();
            $table->string('unit', 50)->nullable();
            $table->decimal('uncertainty', 15, 6)->nullable();
            $table->decimal('tolerance', 15, 6)->nullable();
            $table->enum('pass_fail', ['pass', 'fail'])->nullable();
            $table->json('readings')->nullable()->comment('for multiple readings');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('calibration_id');
            $table->index('measurement_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calibration_data');
    }
};
