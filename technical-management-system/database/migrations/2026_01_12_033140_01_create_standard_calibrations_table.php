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
        Schema::create('standard_calibrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('standard_id')->constrained('standards')->onDelete('restrict');
            $table->date('calibration_date');
            $table->string('certificate_number');
            $table->string('performed_by')->nullable()->comment('External lab name');
            $table->date('next_due_date');
            $table->string('certificate_path', 500)->nullable();
            $table->json('measurement_results')->nullable();
            $table->text('traceability')->nullable();
            $table->decimal('cost', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('standard_id');
            $table->index('calibration_date');
            $table->index('next_due_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('standard_calibrations');
    }
};
