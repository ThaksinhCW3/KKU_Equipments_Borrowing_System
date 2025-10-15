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
        // Update equipment_items condition field to include 'อุปกรณ์ไม่พร้อมใช้งาน'
        Schema::table('equipment_items', function (Blueprint $table) {
            $table->enum('condition', ['สภาพดี', 'สามาถซ่อมได้', 'ไม่สามาถซ่อมได้', 'พัง', 'อุปกรณ์ไม่พร้อมใช้งาน'])->default('สภาพดี')->change();
        });

        // Update equipment_accessories condition field to include 'หาย'
        Schema::table('equipment_accessories', function (Blueprint $table) {
            $table->enum('condition', ['สภาพดี', 'สามาถซ่อมได้', 'ไม่สามาถซ่อมได้', 'พัง', 'หาย'])->default('สภาพดี')->change();
        });

        // Update borrow_request_items condition fields to include 'อุปกรณ์ไม่พร้อมใช้งาน'
        Schema::table('borrow_request_items', function (Blueprint $table) {
            $table->enum('condition_out', ['สภาพดี', 'สามาถซ่อมได้', 'ไม่สามาถซ่อมได้', 'พัง', 'อุปกรณ์ไม่พร้อมใช้งาน'])->default('สภาพดี')->change();
            $table->enum('condition_in', ['สภาพดี', 'สามาถซ่อมได้', 'ไม่สามาถซ่อมได้', 'พัง', 'อุปกรณ์ไม่พร้อมใช้งาน'])->nullable()->change();
        });

        // Update borrow_request_accessories condition fields to include 'หาย'
        Schema::table('borrow_request_accessories', function (Blueprint $table) {
            $table->enum('condition_out', ['สภาพดี', 'สามาถซ่อมได้', 'ไม่สามาถซ่อมได้', 'พัง', 'หาย'])->default('สภาพดี')->change();
            $table->enum('condition_in', ['สภาพดี', 'สามาถซ่อมได้', 'ไม่สามาถซ่อมได้', 'พัง', 'หาย'])->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert equipment_items condition field
        Schema::table('equipment_items', function (Blueprint $table) {
            $table->enum('condition', ['สภาพดี', 'สามาถซ่อมได้', 'ไม่สามาถซ่อมได้', 'พัง'])->default('สภาพดี')->change();
        });

        // Revert equipment_accessories condition field
        Schema::table('equipment_accessories', function (Blueprint $table) {
            $table->enum('condition', ['สภาพดี', 'สามาถซ่อมได้', 'ไม่สามาถซ่อมได้', 'พัง'])->default('สภาพดี')->change();
        });

        // Revert borrow_request_items condition fields
        Schema::table('borrow_request_items', function (Blueprint $table) {
            $table->enum('condition_out', ['สภาพดี', 'สามาถซ่อมได้', 'ไม่สามาถซ่อมได้', 'พัง'])->default('สภาพดี')->change();
            $table->enum('condition_in', ['สภาพดี', 'สามาถซ่อมได้', 'ไม่สามาถซ่อมได้', 'พัง'])->nullable()->change();
        });

        // Revert borrow_request_accessories condition fields
        Schema::table('borrow_request_accessories', function (Blueprint $table) {
            $table->enum('condition_out', ['สภาพดี', 'สามาถซ่อมได้', 'ไม่สามาถซ่อมได้', 'พัง'])->default('สภาพดี')->change();
            $table->enum('condition_in', ['สภาพดี', 'สามาถซ่อมได้', 'ไม่สามาถซ่อมได้', 'พัง'])->nullable()->change();
        });
    }
};
