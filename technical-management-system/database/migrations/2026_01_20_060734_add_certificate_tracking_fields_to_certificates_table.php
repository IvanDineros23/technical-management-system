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
        Schema::table('certificates', function (Blueprint $table) {
            $table->foreignId('job_order_id')->nullable()->after('certificate_number')->constrained('job_orders')->onDelete('restrict');
            $table->timestamp('generated_at')->nullable()->after('notes');
            $table->timestamp('released_at')->nullable()->after('generated_at');
            $table->string('released_to')->nullable()->after('released_at');
            $table->unsignedBigInteger('released_by')->nullable()->after('released_to');
            $table->string('delivery_method')->nullable()->after('released_by');
            $table->text('release_notes')->nullable()->after('delivery_method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            $table->dropForeign(['job_order_id']);
            $table->dropColumn([
                'job_order_id',
                'generated_at',
                'released_at',
                'released_to',
                'released_by',
                'delivery_method',
                'release_notes',
            ]);
        });
    }
};
