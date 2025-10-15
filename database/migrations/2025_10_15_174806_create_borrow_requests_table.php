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
        Schema::create('borrow_requests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('req_id');
            $table->unsignedBigInteger('users_id')->index('borrow_requests_users_id_foreign');
            $table->unsignedBigInteger('equipments_id')->index('borrow_requests_equipments_id_foreign');
            $table->dateTime('start_at')->nullable();
            $table->dateTime('end_at')->nullable();
            $table->timestamp('pickup_deadline')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'check_out', 'check_in', 'cancelled'])->default('pending');
            $table->text('request_reason')->nullable();
            $table->text('request_reason_detail')->nullable();
            $table->string('reject_reason')->nullable();
            $table->string('cancel_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('borrow_requests');
    }
};
