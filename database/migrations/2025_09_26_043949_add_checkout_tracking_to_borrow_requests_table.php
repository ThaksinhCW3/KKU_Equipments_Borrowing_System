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
        Schema::table('borrow_requests', function (Blueprint $table) {
            $table->boolean('is_checked_out')->default(false)->after('status');
            $table->timestamp('checked_out_at')->nullable()->after('is_checked_out');
            $table->timestamp('pickup_deadline')->nullable()->after('checked_out_at');
            $table->string('checked_out_by')->nullable()->after('pickup_deadline'); // Admin who checked out
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('borrow_requests', function (Blueprint $table) {
            $table->dropColumn(['is_checked_out', 'checked_out_at', 'pickup_deadline', 'checked_out_by']);
        });
    }
};
