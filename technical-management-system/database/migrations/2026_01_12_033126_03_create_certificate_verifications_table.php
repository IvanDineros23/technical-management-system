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
        Schema::create('certificate_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('certificate_id')->constrained('certificates')->onDelete('cascade');
            $table->string('certificate_number', 50);
            $table->timestamp('verified_at')->useCurrent();
            $table->string('ip_address', 50)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('location')->nullable();
            $table->enum('verification_result', ['valid', 'invalid', 'expired', 'revoked'])->nullable();
            $table->boolean('qr_scanned')->default(true);
            $table->timestamp('created_at')->useCurrent();
            
            $table->index('certificate_id');
            $table->index('certificate_number');
            $table->index('verified_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificate_verifications');
    }
};
