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
            $table->text('approval_signature')->nullable()->after('approved_at');
            $table->text('approval_comments')->nullable()->after('approval_signature');
            $table->unsignedBigInteger('rejected_by')->nullable()->after('approval_comments');
            $table->timestamp('rejected_at')->nullable()->after('rejected_by');
            $table->text('rejection_reason')->nullable()->after('rejected_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_orders', function (Blueprint $table) {
            $table->dropColumn([
                'approval_signature',
                'approval_comments',
                'rejected_by',
                'rejected_at',
                'rejection_reason',
            ]);
        });
    }
};
