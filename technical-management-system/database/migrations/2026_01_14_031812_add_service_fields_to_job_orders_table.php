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
            $table->string('service_type')->nullable()->after('customer_id');
            $table->text('service_description')->nullable()->after('service_type');
            $table->date('expected_start_date')->nullable()->after('service_description');
            $table->date('expected_completion_date')->nullable()->after('expected_start_date');
            $table->text('service_address')->nullable()->after('expected_completion_date');
            $table->string('city')->nullable()->after('service_address');
            $table->string('province')->nullable()->after('city');
            $table->string('postal_code', 20)->nullable()->after('province');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_orders', function (Blueprint $table) {
            $table->dropColumn([
                'service_type',
                'service_description',
                'expected_start_date',
                'expected_completion_date',
                'service_address',
                'city',
                'province',
                'postal_code'
            ]);
        });
    }
};
