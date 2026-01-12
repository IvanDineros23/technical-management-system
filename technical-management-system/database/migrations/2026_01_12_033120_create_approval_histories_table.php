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
        Schema::create('approval_histories', function (Blueprint $table) {
            $table->id();
            $table->string('approvable_type')->comment('polymorphic');
            $table->unsignedBigInteger('approvable_id');
            $table->unsignedBigInteger('approved_by');
            $table->string('action', 50)->comment('approved, rejected, reviewed');
            $table->string('previous_status', 50)->nullable();
            $table->string('new_status', 50)->nullable();
            $table->text('comments')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('approved_at')->useCurrent();
            $table->timestamps();
            
            $table->index(['approvable_type', 'approvable_id']);
            $table->index('approved_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approval_histories');
    }
};
