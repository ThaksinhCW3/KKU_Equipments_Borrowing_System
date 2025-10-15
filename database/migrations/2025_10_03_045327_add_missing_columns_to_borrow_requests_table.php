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
            // Check if columns don't exist before adding them
            if (!Schema::hasColumn('borrow_requests', 'request_reason')) {
                $table->enum('request_reason', ['assignment', 'personal', 'others'])->nullable()->after('status');
            }
            if (!Schema::hasColumn('borrow_requests', 'assignment_subject')) {
                $table->string('assignment_subject')->nullable()->after('request_reason');
            }
            if (!Schema::hasColumn('borrow_requests', 'assignment_description')) {
                $table->text('assignment_description')->nullable()->after('assignment_subject');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('borrow_requests', function (Blueprint $table) {
            $table->dropColumn(['request_reason', 'assignment_subject', 'assignment_description']);
        });
    }
};
