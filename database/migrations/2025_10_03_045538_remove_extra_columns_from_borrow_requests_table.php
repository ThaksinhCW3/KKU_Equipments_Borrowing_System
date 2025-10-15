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
            // Remove the extra columns, keep only request_reason
            if (Schema::hasColumn('borrow_requests', 'assignment_subject')) {
                $table->dropColumn('assignment_subject');
            }
            if (Schema::hasColumn('borrow_requests', 'assignment_description')) {
                $table->dropColumn('assignment_description');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('borrow_requests', function (Blueprint $table) {
            // Re-add the columns if rollback is needed
            $table->string('assignment_subject')->nullable()->after('request_reason');
            $table->text('assignment_description')->nullable()->after('assignment_subject');
        });
    }
};
