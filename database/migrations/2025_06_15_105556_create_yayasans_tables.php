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
        Schema::create('yayasans', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255); // VARCHAR(255)
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('username');
            $table->foreign('username')->references('username')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->string('password');
            $table->string('role')->default('yayasan'); // Menyimpan ID dari unitpendidikan
            $table->foreign('role')->references('peran_user')->on('role')->onUpdate('restrict')->onDelete('restrict');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('yayasan');
    }
};
