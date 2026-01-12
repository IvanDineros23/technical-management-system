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
        Schema::create('job_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_order_id')->constrained('job_orders')->onDelete('cascade');
            $table->integer('item_number');
            $table->string('equipment_type');
            $table->string('manufacturer')->nullable();
            $table->string('model')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('id_number')->nullable();
            $table->string('range')->nullable();
            $table->string('resolution', 100)->nullable();
            $table->string('accuracy', 100)->nullable();
            $table->string('calibration_type', 100)->nullable()->comment('On-site/In-house');
            $table->integer('calibration_points')->nullable();
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 10, 2)->nullable();
            $table->decimal('total_price', 10, 2)->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            
            $table->index('job_order_id');
            $table->index('serial_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_order_items');
    }
};
