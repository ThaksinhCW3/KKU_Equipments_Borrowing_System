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
        // Update equipment_items condition values
        DB::table('equipment_items')->where('condition', 'สภาพดี')->update(['condition' => 'good']);
        DB::table('equipment_items')->where('condition', 'สามารถซ่อมได้')->update(['condition' => 'repairable']);
        DB::table('equipment_items')->where('condition', 'ไม่สามารถซ่อมได้')->update(['condition' => 'unrepairable']);
        DB::table('equipment_items')->where('condition', 'พัง')->update(['condition' => 'broken']);
        DB::table('equipment_items')->where('condition', 'อุปกรณ์ไม่พร้อมใช้งาน')->update(['condition' => 'unavailable']);

        // Update equipment_accessories condition values
        DB::table('equipment_accessories')->where('condition', 'สภาพดี')->update(['condition' => 'good']);
        DB::table('equipment_accessories')->where('condition', 'สามารถซ่อมได้')->update(['condition' => 'repairable']);
        DB::table('equipment_accessories')->where('condition', 'ไม่สามารถซ่อมได้')->update(['condition' => 'unrepairable']);
        DB::table('equipment_accessories')->where('condition', 'พัง')->update(['condition' => 'broken']);
        DB::table('equipment_accessories')->where('condition', 'อุปกรณ์ไม่พร้อมใช้งาน')->update(['condition' => 'unavailable']);

        // Update borrow_request_items condition_in values
        DB::table('borrow_request_items')->where('condition_in', 'สภาพดี')->update(['condition_in' => 'good']);
        DB::table('borrow_request_items')->where('condition_in', 'สามารถซ่อมได้')->update(['condition_in' => 'repairable']);
        DB::table('borrow_request_items')->where('condition_in', 'ไม่สามารถซ่อมได้')->update(['condition_in' => 'unrepairable']);
        DB::table('borrow_request_items')->where('condition_in', 'พัง')->update(['condition_in' => 'broken']);
        DB::table('borrow_request_items')->where('condition_in', 'อุปกรณ์ไม่พร้อมใช้งาน')->update(['condition_in' => 'unavailable']);
        DB::table('borrow_request_items')->where('condition_in', 'หาย')->update(['condition_in' => 'lost']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert equipment_items condition values
        DB::table('equipment_items')->where('condition', 'good')->update(['condition' => 'สภาพดี']);
        DB::table('equipment_items')->where('condition', 'repairable')->update(['condition' => 'สามารถซ่อมได้']);
        DB::table('equipment_items')->where('condition', 'unrepairable')->update(['condition' => 'ไม่สามารถซ่อมได้']);
        DB::table('equipment_items')->where('condition', 'broken')->update(['condition' => 'พัง']);
        DB::table('equipment_items')->where('condition', 'unavailable')->update(['condition' => 'อุปกรณ์ไม่พร้อมใช้งาน']);

        // Revert equipment_accessories condition values
        DB::table('equipment_accessories')->where('condition', 'good')->update(['condition' => 'สภาพดี']);
        DB::table('equipment_accessories')->where('condition', 'repairable')->update(['condition' => 'สามารถซ่อมได้']);
        DB::table('equipment_accessories')->where('condition', 'unrepairable')->update(['condition' => 'ไม่สามารถซ่อมได้']);
        DB::table('equipment_accessories')->where('condition', 'broken')->update(['condition' => 'พัง']);
        DB::table('equipment_accessories')->where('condition', 'unavailable')->update(['condition' => 'อุปกรณ์ไม่พร้อมใช้งาน']);

        // Revert borrow_request_items condition_in values
        DB::table('borrow_request_items')->where('condition_in', 'good')->update(['condition_in' => 'สภาพดี']);
        DB::table('borrow_request_items')->where('condition_in', 'repairable')->update(['condition_in' => 'สามารถซ่อมได้']);
        DB::table('borrow_request_items')->where('condition_in', 'unrepairable')->update(['condition_in' => 'ไม่สามารถซ่อมได้']);
        DB::table('borrow_request_items')->where('condition_in', 'broken')->update(['condition_in' => 'พัง']);
        DB::table('borrow_request_items')->where('condition_in', 'unavailable')->update(['condition_in' => 'อุปกรณ์ไม่พร้อมใช้งาน']);
        DB::table('borrow_request_items')->where('condition_in', 'lost')->update(['condition_in' => 'หาย']);
    }
};
