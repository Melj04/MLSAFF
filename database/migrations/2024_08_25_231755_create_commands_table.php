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
        // Schema::create('commands', function (Blueprint $table) {
        //     $table->id();
        //     $table->unsignedBigInteger('actuators_id');
        //     $table->foreign('actuators_id')->references('id')->on('actuators')->onDelete('cascade');
        //     $table->string('name',20);
        //     $table->timestamps();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commands');
    }
};
