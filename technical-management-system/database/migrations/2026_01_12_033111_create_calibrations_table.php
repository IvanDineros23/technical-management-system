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
        Schema::create('calibrations', function (Blueprint $table) {
            $table->id();
            $table->string('calibration_number', 50)->unique();
            $table->foreignId('job_order_item_id')->constrained('job_order_items')->onDelete('restrict');
            $table->foreignId('assignment_id')->constrained('assignments')->onDelete('restrict');
            $table->unsignedBigInteger('performed_by')->comment('user_id');
            $table->date('calibration_date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('location')->nullable();
            $table->string('procedure_reference')->nullable();
            $table->json('standards_used')->nullable()->comment('array of standard IDs');
            $table->json('environmental_conditions')->nullable()->comment('temperature, humidity, pressure, conditions_acceptable');
            $table->string('status', 50);
            $table->enum('pass_fail', ['pass', 'fail', 'conditional'])->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            
            $table->index('calibration_number');
            $table->index('job_order_item_id');
            $table->index('performed_by');
            $table->index('calibration_date');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calibrations');
    }
};
