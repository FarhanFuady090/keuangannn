<?php

namespace App\Exports;


use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TagihanSiswaExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */

    protected $siswas;

    public function __construct(Collection $siswas)
    {
        $this->siswas = $siswas;
    }

    public function collection()
    {
        return $this->siswas->map(function ($siswa) {
            $totalTagihan = $siswa->tagihan->sum('nominal');
            $totalDibayar = $siswa->tagihan->sum('jumlah_dibayar');
            $sisa = $totalTagihan - $totalDibayar;

            return [
                'Nama' => $siswa->nama,
                'NIS' => $siswa->nis,
                'Kelas' => optional($siswa->kelas)->nama_kelas,
                'Unit' => optional($siswa->unitPendidikan)->namaUnit,
                'Total Tagihan' => $totalTagihan,
                'Total Dibayar' => $totalDibayar,
                'Sisa' => $sisa,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Nama',
            'NIS',
            'Kelas',
            'Unit',
            'Total Tagihan',
            'Total Dibayar',
            'Sisa',
        ];
    }
}