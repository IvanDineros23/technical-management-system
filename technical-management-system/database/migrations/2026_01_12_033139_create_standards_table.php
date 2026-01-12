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
        Schema::create('standards', function (Blueprint $table) {
            $table->id();
            $table->string('standard_code', 50)->unique();
            $table->string('name');
            $table->string('type', 100)->nullable()->comment('Master/Working/Check');
            $table->string('manufacturer')->nullable();
            $table->string('model')->nullable();
            $table->string('serial_number')->unique()->nullable();
            $table->string('range')->nullable();
            $table->string('accuracy')->nullable();
            $table->string('resolution', 100)->nullable();
            $table->string('certificate_number')->nullable();
            $table->date('calibration_date')->nullable();
            $table->date('next_calibration_date');
            $table->integer('calibration_interval')->default(12)->comment('months');
            $table->text('traceability')->nullable()->comment('NIST, PTB, etc.');
            $table->string('location')->nullable();
            $table->enum('status', ['valid', 'due', 'overdue', 'retired'])->nullable();
            $table->integer('usage_count')->default(0);
            $table->text('notes')->nullable();
            $table->integer('alert_days_before')->default(30);
            $table->timestamps();
            
            $table->index('standard_code');
            $table->index('serial_number');
            $table->index('next_calibration_date');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('standards');
    }
};
