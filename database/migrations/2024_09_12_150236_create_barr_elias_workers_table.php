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
        Schema::create('barr_elias_workers', function (Blueprint $table) {
            $table->id();
           $table->string("shop_name");
            $table->string("street");
            $table->string("more_details");
            $table->string("phone");
            $table->string("profession");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barr_elias_workers');
    }
};