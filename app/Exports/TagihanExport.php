<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithFormatting;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class TagihanExport implements FromCollection, WithHeadings, WithColumnFormatting
{
    protected $tagihans;

    public function __construct($tagihans)
    {
        $this->tagihans = $tagihans;
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->tagihans->map(function ($tagihan) {
            return [
                $tagihan->jenisPembayaran->nama_pembayaran,
                $tagihan->tahunAjaran->tahun_ajaran,
                $tagihan->bulan,
                $tagihan->nominal,
                $tagihan->jumlah_dibayar,
                $tagihan->status,
                $tagihan->tanggal_bayar,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Jenis Pembayaran',
            'Tahun Ajaran',
            'Bulan',
            'Nominal',
            'Jumlah Dibayar',
            'Status',
            'Tanggal Bayar',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'D' => NumberFormat::FORMAT_NUMBER,  // Gunakan format angka biasa dengan koma untuk ribuan
            'E' => NumberFormat::FORMAT_NUMBER,  // Jumlah Dibayar, sama formatnya
        ];
    }
}