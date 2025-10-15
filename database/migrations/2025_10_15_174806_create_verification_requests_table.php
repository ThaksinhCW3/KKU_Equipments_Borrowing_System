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
        Schema::create('verification_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('users_id')->index('fk_verification_requests_users_id');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('student_id_image_path')->nullable();
            $table->text('reject_note')->nullable();
            $table->bigInteger('processed_by')->nullable();
            $table->timestamp('process_at')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('verification_requests');
    }
};
