<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tagihan', function (Blueprint $table) {
            $table->id();

            $table->foreignId('siswa_id')->constrained('siswas')->onDelete('cascade');
            $table->foreignId('jenis_pembayaran_id')->constrained('jenispembayaran')->onDelete('cascade');
            $table->foreignId('tahun_ajaran_id')->constrained('tahunajaran')->onDelete('cascade');

            $table->enum('bulan', [
                'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember', 'Semester 1', 'Semester 2'
            ])->nullable(); // nullable karena ada jenis Bebas

            $table->bigInteger('nominal');
            $table->bigInteger('jumlah_dibayar')->default(0);
            $table->enum('status', ['lunas', 'belum'])->default('belum');
            $table->date('tanggal_bayar')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tagihan');
    }
};
