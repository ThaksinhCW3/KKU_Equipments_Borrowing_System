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
        Schema::create('equipments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code')->unique('code_unique');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('accessories');
            $table->unsignedBigInteger('categories_id')->index('equipments_categories_id_fk');
            $table->enum('status', ['available', 'unavailable', 'maintenance', 'retired'])->default('available');
            $table->text('photo_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipments');
    }
};
