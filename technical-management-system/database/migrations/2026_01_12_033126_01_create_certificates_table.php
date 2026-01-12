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
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->string('certificate_number', 50)->unique();
            $table->foreignId('job_order_item_id')->constrained('job_order_items')->onDelete('restrict');
            $table->foreignId('calibration_id')->constrained('calibrations')->onDelete('restrict');
            $table->date('issue_date');
            $table->date('expiry_date')->nullable();
            $table->date('valid_until')->nullable();
            $table->string('qr_code', 255)->nullable();
            $table->string('qr_code_path')->nullable();
            $table->string('pdf_path', 500)->nullable();
            $table->string('pdf_hash')->nullable()->comment('for integrity check');
            $table->string('template_used', 100)->nullable();
            $table->string('status', 50);
            $table->integer('version')->default(1);
            $table->integer('revision_number')->default(0);
            $table->boolean('is_current')->default(true);
            $table->unsignedBigInteger('issued_by');
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->unsignedBigInteger('supersedes_certificate_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('certificate_number');
            $table->index('job_order_item_id');
            $table->index('calibration_id');
            $table->index('status');
            $table->index('qr_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
