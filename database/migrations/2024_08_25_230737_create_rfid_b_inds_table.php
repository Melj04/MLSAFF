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
        Schema::create('rfid_b_inds', function (Blueprint $table) {
            $table->id(); // `id` INT AUTO_INCREMENT PRIMARY KEY
    $table->string('tag')->unique(); // `tag` VARCHAR(255) NOT NULL UNIQUE
    $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // Foreign key with ON DELETE SET NULL
    $table->timestamps(); // `created_at` and `updated_at` TIMESTAMP NULL
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rfid_b_inds');
    }
};
