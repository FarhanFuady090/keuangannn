<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Siswa extends Model
{
    use HasFactory;
    protected $table = 'siswas';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'nis', 'nisn', 'nama', 'jenis_kelamin', 'kelas_id', 'agama', 'namaOrtu', 'alamatOrtu', 'noTelp', 'email', 'unitpendidikan_id', 'unitpendidikan_idInformal', 'unitpendidikan_idPondok', 'status'];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function unitpendidikan()
    {
        return $this->belongsTo(Unitpendidikan::class);
    }

    public function tagihan()
    {
        return $this->hasMany(Tagihan::class);
    }

    public function tabungan()
    {
        return $this->hasMany(Tabungan::class);
    }
    protected static function booted()
    {
        static::updating(function ($siswa) {
            // Daftar status siswa yang dianggap tidak aktif
            $nonAktifStatuses = ['Non Aktif', 'Lulus', 'Pindah', 'Drop Out'];

            // Cek jika ada perubahan pada status siswa
            if ($siswa->isDirty('status')) {
                if ($siswa->status === 'Aktif') {
                    // Jika status siswa menjadi Aktif, aktifkan kembali tabungan (restore dari soft delete)
                    $siswa->tabungan()->withTrashed()->restore();

                    // Opsional: set status tabungan jika ada kolom status
                    $siswa->tabungan()->update(['status' => 'Aktif']);
                } elseif (in_array($siswa->status, $nonAktifStatuses)) {
                    // Jika siswa menjadi tidak aktif, non-aktifkan tabungan dan soft delete
                    $siswa->tabungan()->update([
                        'status' => 'Non Aktif',
                        'deleted_by' => Auth::check() ? Auth::user()->username : 'system', // jika tidak via login
                    ]);

                    $siswa->tabungan()->delete(); // soft delete
                }
            }
        });
    }
}