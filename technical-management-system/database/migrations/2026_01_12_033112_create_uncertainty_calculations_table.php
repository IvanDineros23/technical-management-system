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
        Schema::create('uncertainty_calculations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calibration_id')->constrained('calibrations')->onDelete('cascade');
            $table->string('component')->comment('e.g., standard, resolution, repeatability');
            $table->decimal('value', 15, 8);
            $table->string('distribution', 50)->nullable()->comment('normal, rectangular, triangular');
            $table->decimal('divisor', 5, 2)->nullable();
            $table->decimal('standard_uncertainty', 15, 8)->nullable();
            $table->decimal('sensitivity_coefficient', 10, 4)->nullable();
            $table->decimal('uncertainty_contribution', 15, 8)->nullable();
            $table->timestamps();
            
            $table->index('calibration_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uncertainty_calculations');
    }
};
