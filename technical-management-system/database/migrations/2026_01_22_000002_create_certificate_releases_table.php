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
        Schema::create('certificate_releases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('certificate_id')->constrained()->onDelete('cascade');
            $table->foreignId('released_by')->constrained('users')->onDelete('restrict');
            $table->timestamp('released_at');
            $table->string('released_to');
            $table->enum('delivery_method', ['pickup', 'courier', 'email', 'hand_delivery']);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('certificate_id');
            $table->index('released_by');
            $table->index('released_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificate_releases');
    }
};
