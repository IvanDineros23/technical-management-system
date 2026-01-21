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
        // Add signatory fields to calibrations table
        Schema::table('calibrations', function (Blueprint $table) {
            // Add signatory tracking fields
            $table->foreignId('signatory_id')->nullable()->after('status')->constrained('users')->onDelete('restrict')->comment('user_id of signatory who reviewed');
            $table->text('signatory_remarks')->nullable()->after('signatory_id')->comment('remarks from signatory during review');
            $table->timestamp('reviewed_at')->nullable()->after('signatory_remarks')->comment('when signatory reviewed the calibration');
            $table->foreignId('certificate_id')->nullable()->after('reviewed_at')->constrained('certificates')->onDelete('restrict')->comment('linked certificate after signing');
            
            // Add indexes for performance
            $table->index('signatory_id');
            $table->index('certificate_id');
        });

        // Add signature fields to certificates table
        Schema::table('certificates', function (Blueprint $table) {
            // Digital signature tracking
            $table->foreignId('signed_by')->nullable()->after('approved_by')->constrained('users')->onDelete('restrict')->comment('user_id of signatory who signed');
            $table->timestamp('signed_at')->nullable()->after('signed_by')->comment('when certificate was digitally signed');
            $table->json('data')->nullable()->after('signed_at')->comment('full certificate data snapshot for integrity');
            
            // Add indexes for performance
            $table->index('signed_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('calibrations', function (Blueprint $table) {
            $table->dropForeignIdFor('User', 'signatory_id');
            $table->dropForeignIdFor('Certificate');
            $table->dropIndex(['signatory_id']);
            $table->dropIndex(['certificate_id']);
            $table->dropColumn(['signatory_id', 'signatory_remarks', 'reviewed_at', 'certificate_id']);
        });

        Schema::table('certificates', function (Blueprint $table) {
            $table->dropForeignIdFor('User', 'signed_by');
            $table->dropIndex(['signed_by']);
            $table->dropColumn(['signed_by', 'signed_at', 'data']);
        });
    }
};
