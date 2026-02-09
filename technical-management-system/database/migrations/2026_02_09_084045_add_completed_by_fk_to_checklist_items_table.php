<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE checklist_items MODIFY completed_by BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE checklist_items ADD CONSTRAINT checklist_items_completed_by_foreign FOREIGN KEY (completed_by) REFERENCES users(id)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE checklist_items DROP FOREIGN KEY checklist_items_completed_by_foreign');
        DB::statement('ALTER TABLE checklist_items MODIFY completed_by VARCHAR(255) NULL');
    }
};
