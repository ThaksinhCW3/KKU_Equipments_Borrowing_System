<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Make equipment_item_id nullable to align with borrow_request_items linking
        if (Schema::hasTable('borrow_requests')) {
            try {
                DB::statement('ALTER TABLE borrow_requests MODIFY equipment_item_id BIGINT UNSIGNED NULL');
            } catch (\Throwable $e) {
                // fallback for some MySQL variants using different syntax
                try {
                    DB::statement('ALTER TABLE borrow_requests ALTER COLUMN equipment_item_id DROP NOT NULL');
                } catch (\Throwable $e2) {
                    // ignore if already nullable
                }
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('borrow_requests')) {
            try {
                DB::statement('ALTER TABLE borrow_requests MODIFY equipment_item_id BIGINT UNSIGNED NOT NULL');
            } catch (\Throwable $e) {
                // ignore if cannot revert
            }
        }
    }
};


