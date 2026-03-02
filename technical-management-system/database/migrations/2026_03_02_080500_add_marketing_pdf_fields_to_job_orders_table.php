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
        Schema::table('job_orders', function (Blueprint $table) {
            $table->string('client_po_ctrl_no')->nullable()->after('requested_by');
            $table->text('terms')->nullable()->after('client_po_ctrl_no');
            $table->string('service_invoice_number')->nullable()->after('terms');
            $table->string('other_details')->nullable()->after('service_invoice_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_orders', function (Blueprint $table) {
            $table->dropColumn([
                'client_po_ctrl_no',
                'terms',
                'service_invoice_number',
                'other_details',
            ]);
        });
    }
};
