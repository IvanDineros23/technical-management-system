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
            $table->string('pdf_filename')->nullable()->after('rejection_reason')->comment('Generated PDF form filename');
            $table->string('pdf_path')->nullable()->after('pdf_filename')->comment('Full path to generated PDF form');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_orders', function (Blueprint $table) {
            $table->dropColumn(['pdf_filename', 'pdf_path']);
        });
    }
};
