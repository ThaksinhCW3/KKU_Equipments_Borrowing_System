<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, update existing data to use Thai values
        DB::table('equipment_items')->where('condition', 'Good')->update(['condition' => 'สภาพดี']);
        DB::table('equipment_accessories')->where('condition', 'Good')->update(['condition' => 'สภาพดี']);
        DB::table('borrow_request_items')->where('condition_out', 'Good')->update(['condition_out' => 'สภาพดี']);
        DB::table('borrow_request_accessories')->where('condition_out', 'Good')->update(['condition_out' => 'สภาพดี']);

        // Update equipment_items condition field
        Schema::table('equipment_items', function (Blueprint $table) {
            $table->enum('condition', ['สภาพดี', 'สามาถซ่อมได้', 'ไม่สามาถซ่อมได้'])->default('สภาพดี')->change();
        });

        // Update equipment_accessories condition field
        Schema::table('equipment_accessories', function (Blueprint $table) {
            $table->enum('condition', ['สภาพดี', 'สามาถซ่อมได้', 'ไม่สามาถซ่อมได้'])->default('สภาพดี')->change();
        });

        // Update borrow_request_items condition fields
        Schema::table('borrow_request_items', function (Blueprint $table) {
            $table->enum('condition_out', ['สภาพดี', 'สามาถซ่อมได้', 'ไม่สามาถซ่อมได้'])->default('สภาพดี')->change();
            $table->enum('condition_in', ['สภาพดี', 'สามาถซ่อมได้', 'ไม่สามาถซ่อมได้'])->nullable()->change();
        });

        // Update borrow_request_accessories condition fields
        Schema::table('borrow_request_accessories', function (Blueprint $table) {
            $table->enum('condition_out', ['สภาพดี', 'สามาถซ่อมได้', 'ไม่สามาถซ่อมได้'])->default('สภาพดี')->change();
            $table->enum('condition_in', ['สภาพดี', 'สามาถซ่อมได้', 'ไม่สามาถซ่อมได้'])->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to varchar fields
        Schema::table('equipment_items', function (Blueprint $table) {
            $table->string('condition')->default('Good')->change();
        });

        Schema::table('equipment_accessories', function (Blueprint $table) {
            $table->string('condition')->default('Good')->change();
        });

        Schema::table('borrow_request_items', function (Blueprint $table) {
            $table->string('condition_out')->default('Good')->change();
            $table->string('condition_in')->nullable()->change();
        });

        Schema::table('borrow_request_accessories', function (Blueprint $table) {
            $table->string('condition_out')->default('Good')->change();
            $table->string('condition_in')->nullable()->change();
        });

        // Revert data back to English
        DB::table('equipment_items')->where('condition', 'สภาพดี')->update(['condition' => 'Good']);
        DB::table('equipment_accessories')->where('condition', 'สภาพดี')->update(['condition' => 'Good']);
        DB::table('borrow_request_items')->where('condition_out', 'สภาพดี')->update(['condition_out' => 'Good']);
        DB::table('borrow_request_accessories')->where('condition_out', 'สภาพดี')->update(['condition_out' => 'Good']);
    }
};
