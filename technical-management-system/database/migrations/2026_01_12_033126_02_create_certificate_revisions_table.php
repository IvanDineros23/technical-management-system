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
        Schema::create('certificate_revisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('certificate_id')->constrained('certificates')->onDelete('cascade');
            $table->integer('version');
            $table->text('revision_reason');
            $table->text('changes_made')->nullable();
            $table->string('previous_pdf_path', 500)->nullable();
            $table->string('revised_pdf_path', 500)->nullable();
            $table->unsignedBigInteger('revised_by');
            $table->timestamp('revised_at')->useCurrent();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            
            $table->index('certificate_id');
            $table->index('version');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificate_revisions');
    }
};
