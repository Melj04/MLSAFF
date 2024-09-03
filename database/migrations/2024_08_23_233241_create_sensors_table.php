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
        // Schema::create('sensors', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('name');
        //     $table->string('key', 64); // Store as hex (32 bytes * 2)
        //     $table->string('nonce', 24); // Store as hex (12 bytes * 2)
        //     $table->timestamps();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sensors');
    }
};
