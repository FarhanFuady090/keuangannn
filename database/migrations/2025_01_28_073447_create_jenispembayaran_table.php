<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jenispembayaran', function (Blueprint $table) {
            $table->id(); // gunakan standar Laravel
            $table->string('nama_pembayaran', 255);
            $table->enum('type', ['Bulanan', 'Semester', 'Tahunan', 'Bebas']);
            $table->bigInteger('nominal_jenispembayaran');
            $table->enum('status', ['Aktif', 'Non Aktif']);

            $table->foreignId('idunitpendidikan')
                ->constrained('unitpendidikan')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreignId('id_tahunajaran')
                ->constrained('tahunajaran')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jenispembayaran');
    }
};
