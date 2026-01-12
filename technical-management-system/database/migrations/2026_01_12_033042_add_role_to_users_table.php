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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->after('password')->constrained('roles')->onDelete('restrict');
            $table->string('department', 100)->nullable()->after('role_id');
            $table->string('employee_id', 50)->unique()->nullable()->after('department');
            $table->string('signature_path')->nullable()->after('employee_id');
            $table->string('phone', 50)->nullable()->after('signature_path');
            $table->boolean('is_active')->default(true)->after('phone');
            $table->timestamp('last_login_at')->nullable()->after('is_active');
            
            $table->index('role_id');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropIndex(['role_id']);
            $table->dropIndex(['is_active']);
            $table->dropColumn([
                'role_id',
                'department',
                'employee_id',
                'signature_path',
                'phone',
                'is_active',
                'last_login_at'
            ]);
        });
    }
};
