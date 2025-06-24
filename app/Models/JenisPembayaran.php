<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisPembayaran extends Model
{
    use HasFactory;

    protected $table = 'jenispembayaran';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nama_pembayaran',
        'type',
        'id_tahunajaran',
        'nominal_jenispembayaran',
        'status',
        'idunitpendidikan',
    ];

    public function unitPendidikan()
    {
        return $this->belongsTo(UnitPendidikan::class, 'idunitpendidikan');
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'id_tahunajaran');
    }

    public function tagihan()
    {
        return $this->hasMany(Tagihan::class, 'jenis_pembayaran_id');
    }
}
