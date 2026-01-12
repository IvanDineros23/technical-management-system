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
        Schema::create('accounting_releases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('release_id')->unique()->constrained('releases')->onDelete('cascade');
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->enum('payment_status', ['unpaid', 'partial', 'paid', 'overdue']);
            $table->decimal('amount_due', 10, 2)->nullable();
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->date('payment_date')->nullable();
            $table->string('payment_method', 100)->nullable();
            $table->string('payment_reference')->nullable();
            $table->unsignedBigInteger('verified_by');
            $table->timestamp('verified_at')->useCurrent();
            $table->boolean('can_release')->default(false);
            $table->text('remarks')->nullable();
            $table->timestamps();
            
            $table->index('release_id');
            $table->index('payment_status');
            $table->index('verified_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounting_releases');
    }
};
