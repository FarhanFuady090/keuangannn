<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayaran_tagihan', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tagihan_id')
                  ->constrained('tagihan')
                  ->onDelete('cascade');

            $table->date('tanggal_bayar'); // kapan pembayaran dilakukan
            $table->bigInteger('jumlah_dibayar'); // nominal yang dibayar

            $table->string('dibayarkan_oleh')->nullable(); // opsional: wali murid / siswa
            $table->string('input_by')->nullable(); // username TU/Petugas input
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran_tagihan');
    }
};
