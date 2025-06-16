<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembayaranTagihan extends Model
{
    use HasFactory;

    protected $table = 'pembayaran_tagihan';

    protected $fillable = [
        'tagihan_id',
        'tanggal_bayar',
        'jumlah_dibayar',
        'dibayarkan_oleh',
        'input_by',
    ];

    /**
     * Relasi ke Tagihan
     */
    public function tagihan()
    {
        return $this->belongsTo(Tagihan::class);
    }

    /**
     * Event saat pembayaran dibuat:
     * Akumulasi otomatis ke tagihan → jumlah_dibayar dan status
     */
    protected static function booted()
    {
        static::created(function ($pembayaran) {
            $tagihan = $pembayaran->tagihan;

            // Hitung total pembayaran dari semua riwayat pembayaran
            $totalDibayar = $tagihan->pembayaran()->sum('jumlah_dibayar');

            // Update tagihan
            $tagihan->update([
                'jumlah_dibayar' => $totalDibayar,
                'status' => $totalDibayar >= $tagihan->nominal ? 'lunas' : 'belum',
                'tanggal_bayar' => $totalDibayar >= $tagihan->nominal ? now() : null,
            ]);
        });
    }
}
