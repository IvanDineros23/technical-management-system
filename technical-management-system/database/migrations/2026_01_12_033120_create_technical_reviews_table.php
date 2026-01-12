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
        Schema::create('technical_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calibration_id')->constrained('calibrations')->onDelete('restrict');
            $table->unsignedBigInteger('reviewer_id')->comment('user_id');
            $table->date('review_date');
            $table->time('review_time')->nullable();
            $table->enum('result', ['approved', 'rejected', 'conditional'])->nullable();
            $table->text('findings')->nullable();
            $table->text('recommendations')->nullable();
            $table->boolean('data_reviewed')->default(false);
            $table->boolean('calculations_verified')->default(false);
            $table->boolean('standards_checked')->default(false);
            $table->string('status', 50)->nullable();
            $table->timestamps();
            
            $table->index('calibration_id');
            $table->index('reviewer_id');
            $table->index('result');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('technical_reviews');
    }
};
