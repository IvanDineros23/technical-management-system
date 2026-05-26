<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('equipment_requests', function (Blueprint $table) {
            $table->foreignId('job_order_id')->nullable()->constrained('job_orders')->nullOnDelete()->after('equipment_id');
            $table->timestamp('returned_at')->nullable()->after('approved_at');
        });

    }

    public function down(): void
    {
        Schema::table('equipment_requests', function (Blueprint $table) {
            $table->dropForeign(['job_order_id']);
            $table->dropColumn(['job_order_id', 'returned_at']);
        });

    }
};
