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
        Schema::create('calibration_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calibration_id')->constrained('calibrations')->onDelete('restrict');
            $table->string('report_number', 50)->unique();
            $table->string('file_name');
            $table->string('file_path', 500);
            $table->integer('file_size')->nullable();
            $table->integer('version')->default(1);
            $table->string('status', 50)->nullable();
            $table->unsignedBigInteger('uploaded_by');
            $table->timestamp('uploaded_at')->useCurrent();
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
            
            $table->index('calibration_id');
            $table->index('report_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calibration_reports');
    }
};
