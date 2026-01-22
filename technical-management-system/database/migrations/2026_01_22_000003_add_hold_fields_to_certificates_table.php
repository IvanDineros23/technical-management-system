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
            // Add hold functionality fields if they don't exist
            if (!Schema::hasColumn('certificates', 'is_on_hold')) {
                $table->boolean('is_on_hold')->default(false)->after('status');
            }
            if (!Schema::hasColumn('certificates', 'hold_reason')) {
                $table->text('hold_reason')->nullable()->after('is_on_hold');
            }
            if (!Schema::hasColumn('certificates', 'held_by')) {
                $table->foreignId('held_by')->nullable()->after('hold_reason')->constrained('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('certificates', 'held_at')) {
                $table->timestamp('held_at')->nullable()->after('held_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            $table->dropColumn(['is_on_hold', 'hold_reason', 'held_by', 'held_at']);
        });
    }
};
