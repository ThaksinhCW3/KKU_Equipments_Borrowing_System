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
        // Update equipment_items condition field to include 'พัง'
        Schema::table('equipment_items', function (Blueprint $table) {
            $table->enum('condition', ['สภาพดี', 'สามาถซ่อมได้', 'ไม่สามาถซ่อมได้', 'พัง'])->default('สภาพดี')->change();
        });

        // Update equipment_accessories condition field to include 'พัง'
        Schema::table('equipment_accessories', function (Blueprint $table) {
            $table->enum('condition', ['สภาพดี', 'สามาถซ่อมได้', 'ไม่สามาถซ่อมได้', 'พัง'])->default('สภาพดี')->change();
        });

        // Update borrow_request_items condition fields to include 'พัง'
        Schema::table('borrow_request_items', function (Blueprint $table) {
            $table->enum('condition_out', ['สภาพดี', 'สามาถซ่อมได้', 'ไม่สามาถซ่อมได้', 'พัง'])->default('สภาพดี')->change();
            $table->enum('condition_in', ['สภาพดี', 'สามาถซ่อมได้', 'ไม่สามาถซ่อมได้', 'พัง'])->nullable()->change();
        });

        // Update borrow_request_accessories condition fields to include 'พัง'
        Schema::table('borrow_request_accessories', function (Blueprint $table) {
            $table->enum('condition_out', ['สภาพดี', 'สามาถซ่อมได้', 'ไม่สามาถซ่อมได้', 'พัง'])->default('สภาพดี')->change();
            $table->enum('condition_in', ['สภาพดี', 'สามาถซ่อมได้', 'ไม่สามาถซ่อมได้', 'พัง'])->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert equipment_items condition field
        Schema::table('equipment_items', function (Blueprint $table) {
            $table->enum('condition', ['สภาพดี', 'สามาถซ่อมได้', 'ไม่สามาถซ่อมได้'])->default('สภาพดี')->change();
        });

        // Revert equipment_accessories condition field
        Schema::table('equipment_accessories', function (Blueprint $table) {
            $table->enum('condition', ['สภาพดี', 'สามาถซ่อมได้', 'ไม่สามาถซ่อมได้'])->default('สภาพดี')->change();
        });

        // Revert borrow_request_items condition fields
        Schema::table('borrow_request_items', function (Blueprint $table) {
            $table->enum('condition_out', ['สภาพดี', 'สามาถซ่อมได้', 'ไม่สามาถซ่อมได้'])->default('สภาพดี')->change();
            $table->enum('condition_in', ['สภาพดี', 'สามาถซ่อมได้', 'ไม่สามาถซ่อมได้'])->nullable()->change();
        });

        // Revert borrow_request_accessories condition fields
        Schema::table('borrow_request_accessories', function (Blueprint $table) {
            $table->enum('condition_out', ['สภาพดี', 'สามาถซ่อมได้', 'ไม่สามาถซ่อมได้'])->default('สภาพดี')->change();
            $table->enum('condition_in', ['สภาพดี', 'สามาถซ่อมได้', 'ไม่สามาถซ่อมได้'])->nullable()->change();
        });
    }
};
