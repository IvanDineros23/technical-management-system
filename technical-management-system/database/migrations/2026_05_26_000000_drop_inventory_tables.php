<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('inventory_requests');
        Schema::dropIfExists('inventory_items');
    }

    public function down(): void
    {
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('sku')->unique();
            $table->string('category')->nullable();
            $table->integer('quantity')->default(0);
            $table->string('unit')->default('units');
            $table->integer('min_level')->default(0);
            $table->enum('status', ['normal', 'low', 'out'])->default('normal');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('category');
        });

        Schema::create('inventory_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_item_id')->constrained()->onDelete('cascade');
            $table->foreignId('requested_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->integer('quantity');
            $table->text('purpose');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('requested_by');
        });
    }
};
