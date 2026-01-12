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
        Schema::create('signatory_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calibration_id')->constrained('calibrations')->onDelete('restrict');
            $table->unsignedBigInteger('signatory_id')->comment('user_id');
            $table->enum('approval_level', ['reviewer', 'approver']);
            $table->integer('approval_order')->default(1);
            $table->timestamp('approved_at')->nullable();
            $table->string('signature_path')->nullable();
            $table->text('signature_data')->nullable()->comment('base64 encoded signature');
            $table->text('comments')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
            
            $table->index('calibration_id');
            $table->index('signatory_id');
            $table->index('status');
            $table->index('approval_level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('signatory_approvals');
    }
};
