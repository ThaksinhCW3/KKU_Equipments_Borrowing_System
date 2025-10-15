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
        Schema::create('borrow_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('borrow_requests_id')->index('borrow_transaction_borrow_requests_id_foreign');
            $table->dateTime('checked_out_at')->nullable();
            $table->dateTime('checked_in_at')->nullable();
            $table->integer('penalty_amount')->default(0);
            $table->enum('penalty_check', ['paid', 'unpaid'])->default('unpaid')->comment('to check penalty payment');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('borrow_transactions');
    }
};
