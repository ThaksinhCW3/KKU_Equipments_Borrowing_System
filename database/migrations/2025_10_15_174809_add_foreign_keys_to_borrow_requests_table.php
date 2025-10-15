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
            $table->foreign(['equipments_id'])->references(['id'])->on('equipments')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['users_id'])->references(['id'])->on('users')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('borrow_requests', function (Blueprint $table) {
            $table->dropForeign('borrow_requests_equipments_id_foreign');
            $table->dropForeign('borrow_requests_users_id_foreign');
        });
    }
};
