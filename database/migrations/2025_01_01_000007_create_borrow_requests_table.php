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
            $table->id();
            $table->string('req_id');
            $table->foreignId('users_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('equipments_id')->constrained('equipments')->onDelete('cascade');
            $table->datetime('start_at')->nullable();
            $table->datetime('end_at')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'check_out', 'check_in', 'cancelled'])->default('pending');
            $table->text('request_reason');
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
