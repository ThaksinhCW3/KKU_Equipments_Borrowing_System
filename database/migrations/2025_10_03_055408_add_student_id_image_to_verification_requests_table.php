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
        Schema::table('verification_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('verification_requests', 'student_id_image_path')) {
                $table->string('student_id_image_path')->nullable()->after('users_id');
            }
            if (!Schema::hasColumn('verification_requests', 'admin_note')) {
                $table->text('admin_note')->nullable()->after('student_id_image_path');
            }
            if (!Schema::hasColumn('verification_requests', 'processed_by')) {
                $table->unsignedBigInteger('processed_by')->nullable()->after('admin_note');
            }
            if (!Schema::hasColumn('verification_requests', 'process_at')) {
                $table->timestamp('process_at')->nullable()->after('processed_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('verification_requests', function (Blueprint $table) {
            $table->dropColumn(['student_id_image_path', 'admin_note', 'processed_by', 'process_at']);
        });
    }
};
