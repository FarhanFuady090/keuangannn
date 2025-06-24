<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tagihan extends Model
{
    use HasFactory;

    protected $table = 'tagihan';

    protected $fillable = [
        'siswa_id',
        'jenis_pembayaran_id',
        'tahun_ajaran_id',
        'bulan',
        'nominal',
        'jumlah_dibayar',
        'status',
        'tanggal_bayar',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function jenisPembayaran()
    {
        return $this->belongsTo(JenisPembayaran::class, 'jenis_pembayaran_id');
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    }

    public function pembayaran()
    {
        return $this->hasMany(PembayaranTagihan::class);
    }
}
