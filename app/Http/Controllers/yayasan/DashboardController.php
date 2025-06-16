<?php

namespace App\Http\Controllers\yayasan;

use App\Http\Controllers\Controller;
use App\Models\Tabungan;
use App\Models\Siswa;
use App\Models\UnitPendidikan;
use App\Models\TransaksiTabungan;
use App\Models\TransaksiKas;
use App\Models\Kas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema; // Tambahkan import ini

class DashboardController extends Controller
{
    public function index()
    {
        $siswaAktif = Siswa::where('status', 'Aktif')->count();
        $siswaNonAktif = Siswa::where('status', 'Non Aktif')->count();
        $totalSiswa = Siswa::count();

        $totalKasMasuk = TransaksiKas::join('kas', 'transaksi_kas.kas_id', '=', 'kas.id')
            ->where('kas.kategori', 'Pemasukan')
            ->where('kas.status', 'Aktif')
            ->sum('nominal');

        $totalKasKeluar = TransaksiKas::join('kas', 'transaksi_kas.kas_id', '=', 'kas.id')
            ->where('kas.kategori', 'Pengeluaran')
            ->where('kas.status', 'Aktif')
            ->sum('nominal');

        $totalKas = $totalKasMasuk - $totalKasKeluar;

        $totalSetoranTransaksi = TransaksiTabungan::join('tabungans', 'transaksi_tabungans.tabungan_id', '=', 'tabungans.id')
            ->where('transaksi_tabungans.jenis_transaksi', 'Setoran')
            ->where('tabungans.status', 'Aktif')
            ->sum('transaksi_tabungans.jumlah');

        $totalSetoranAwal = Tabungan::where('status', 'Aktif')->sum('saldo_awal');

        $totalTabunganMasuk = $totalSetoranTransaksi + $totalSetoranAwal;

        $totalTabunganKeluar = TransaksiTabungan::join('tabungans', 'transaksi_tabungans.tabungan_id', '=', 'tabungans.id')
            ->where('transaksi_tabungans.jenis_transaksi', 'Penarikan')
            ->where('tabungans.status', 'Aktif')
            ->sum('transaksi_tabungans.jumlah');

        // Hitung saldo akhir berdasarkan saldo_awal + setoran - penarikan
        $totalTabunganAkhir = Tabungan::where('status', 'Aktif')->get()->sum(function ($tabungan) {
            $setoran = TransaksiTabungan::where('tabungan_id', $tabungan->id)
                ->where('jenis_transaksi', 'Setoran')
                ->sum('jumlah');
            $penarikan = TransaksiTabungan::where('tabungan_id', $tabungan->id)
                ->where('jenis_transaksi', 'Penarikan')
                ->sum('jumlah');
            return $tabungan->saldo_awal + $setoran - $penarikan;
        });

        $totalPemasukan = $totalKasMasuk;
        $totalPengeluaran = $totalKasKeluar;
        $total = $totalPemasukan - $totalPengeluaran;
        $totalUnit = UnitPendidikan::where('status', operator: 'Aktif')->count();

        $siswaPerUnit = Siswa::select(
            'unitpendidikan_id',
            DB::raw("SUM(CASE WHEN status = 'Aktif' THEN 1 ELSE 0 END) AS aktif"),
            DB::raw("SUM(CASE WHEN status = 'Non Aktif' THEN 1 ELSE 0 END) AS non_aktif"),
            DB::raw("SUM(CASE WHEN status = 'Drop Out' THEN 1 ELSE 0 END) AS drop_out"),
            DB::raw("SUM(CASE WHEN status = 'Lulus' THEN 1 ELSE 0 END) AS lulus"),
            DB::raw("SUM(CASE WHEN status = 'Pindah' THEN 1 ELSE 0 END) AS pindah"),
            DB::raw("COUNT(*) AS total")
        )
            ->whereNotNull('unitpendidikan_id')
            ->groupBy('unitpendidikan_id')
            ->with('unitpendidikan:id,namaunit')
            ->get();

        // PERBAIKAN: Ambil semua unit aktif dengan data keuangan
        $allUnits = UnitPendidikan::where('status', 'Aktif')->get();

        $keuanganPerUnit = collect();

        foreach ($allUnits as $unit) {
            $unitId = $unit->id;

            // Total Saldo Masuk (Setoran + Saldo Awal)
            $totalSetoranTransaksi = TransaksiTabungan::join('tabungans', 'transaksi_tabungans.tabungan_id', '=', 'tabungans.id')
                ->join('siswas', 'tabungans.siswa_id', '=', 'siswas.id')
                ->where('transaksi_tabungans.jenis_transaksi', 'Setoran')
                ->where('tabungans.status', 'Aktif')
                ->where('siswas.unitpendidikan_id', $unitId)
                ->sum('transaksi_tabungans.jumlah');

            $totalSaldoAwal = Tabungan::join('siswas', 'tabungans.siswa_id', '=', 'siswas.id')
                ->where('tabungans.status', 'Aktif')
                ->where('siswas.unitpendidikan_id', $unitId)
                ->sum('tabungans.saldo_awal');

            $totalSaldoMasuk = $totalSetoranTransaksi + $totalSaldoAwal;

            // Total Kas Masuk - cek apakah kolom unitpendidikan_id ada
            $totalKasMasuk = 0;
            if (Schema::hasColumn('transaksi_kas', 'unitpendidikan_id')) {
                $totalKasMasuk = TransaksiKas::join('kas', 'transaksi_kas.kas_id', '=', 'kas.id')
                    ->where('kas.kategori', 'Pemasukan')
                    ->where('kas.status', 'Aktif')
                    ->where('transaksi_kas.unitpendidikan_id', $unitId)
                    ->sum('transaksi_kas.nominal');
            }

            // Total Tagihan Terbayar (placeholder - sesuaikan dengan tabel tagihan Anda)
            $totalTagihanTerbayar = 0;

            // Total Saldo Keluar
            $totalSaldoKeluar = TransaksiTabungan::join('tabungans', 'transaksi_tabungans.tabungan_id', '=', 'tabungans.id')
                ->join('siswas', 'tabungans.siswa_id', '=', 'siswas.id')
                ->where('transaksi_tabungans.jenis_transaksi', 'Penarikan')
                ->where('tabungans.status', 'Aktif')
                ->where('siswas.unitpendidikan_id', $unitId)
                ->sum('transaksi_tabungans.jumlah');

            // Total Kas Keluar - cek apakah kolom unitpendidikan_id ada
            $totalKasKeluar = 0;
            if (Schema::hasColumn('transaksi_kas', 'unitpendidikan_id')) {
                $totalKasKeluar = TransaksiKas::join('kas', 'transaksi_kas.kas_id', '=', 'kas.id')
                    ->where('kas.kategori', 'Pengeluaran')
                    ->where('kas.status', 'Aktif')
                    ->where('transaksi_kas.unitpendidikan_id', $unitId)
                    ->sum('transaksi_kas.nominal');
            }

            // Total Tagihan Belum Terbayar (placeholder)
            $totalTagihanBelumTerbayar = 0;

            // Total Saldo Akhir
            $totalSaldoAkhir = Tabungan::join('siswas', 'tabungans.siswa_id', '=', 'siswas.id')
                ->where('tabungans.status', 'Aktif')
                ->where('siswas.unitpendidikan_id', $unitId)
                ->get()
                ->sum(function ($tabungan) {
                    $setoran = TransaksiTabungan::where('tabungan_id', $tabungan->id)
                        ->where('jenis_transaksi', 'Setoran')
                        ->sum('jumlah');
                    $penarikan = TransaksiTabungan::where('tabungan_id', $tabungan->id)
                        ->where('jenis_transaksi', 'Penarikan')
                        ->sum('jumlah');
                    return $tabungan->saldo_awal + $setoran - $penarikan;
                });

            // Hanya tambahkan unit yang memiliki data keuangan
            if ($totalSaldoMasuk > 0 || $totalKasMasuk > 0 || $totalSaldoKeluar > 0 || $totalKasKeluar > 0 || $totalSaldoAkhir > 0) {
                $keuanganPerUnit->push((object) [
                    'unitpendidikan_id' => $unitId,
                    'unitpendidikan' => (object) [
                        'id' => $unit->id,
                        'namaunit' => $unit->namaunit ?? 'Unit ' . $unit->id
                    ],
                    'total_saldo_masuk' => $totalSaldoMasuk,
                    'total_kas_masuk' => $totalKasMasuk,
                    'total_tagihan_terbayar' => $totalTagihanTerbayar,
                    'total_saldo_keluar' => $totalSaldoKeluar,
                    'total_kas_keluar' => $totalKasKeluar,
                    'total_tagihan_belum_terbayar' => $totalTagihanBelumTerbayar,
                    'total_saldo_akhir' => $totalSaldoAkhir,
                    'total_kas' => $totalKasMasuk - $totalKasKeluar,
                    'total_tagihan' => $totalTagihanTerbayar + $totalTagihanBelumTerbayar,
                    'total_pemasukan' => $totalSaldoMasuk + $totalKasMasuk + $totalTagihanTerbayar,
                    'total_pengeluaran' => $totalSaldoKeluar + $totalKasKeluar,
                    'total_akhir' => $totalSaldoMasuk + $totalKasMasuk + $totalTagihanTerbayar - ($totalSaldoKeluar + $totalKasKeluar)
                ]);
            }
        }

        // Data untuk chart
        $setoranTransaksi = TransaksiTabungan::join('tabungans', 'transaksi_tabungans.tabungan_id', '=', 'tabungans.id')
            ->where('transaksi_tabungans.jenis_transaksi', 'Setoran')
            ->where('tabungans.status', 'Aktif')
            ->selectRaw('MONTH(transaksi_tabungans.created_at) as month, SUM(transaksi_tabungans.jumlah) as total')
            ->groupBy(DB::raw('MONTH(transaksi_tabungans.created_at)'))
            ->pluck('total', 'month');

        $setoranAwal = Tabungan::where('status', 'Aktif')
            ->selectRaw('MONTH(created_at) as month, SUM(saldo_awal) as total')
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->pluck('total', 'month');

        $setoranGabungan = [];
        for ($i = 1; $i <= 12; $i++) {
            $transaksi = $setoranTransaksi->get($i, 0);
            $awal = $setoranAwal->get($i, 0);
            $setoranGabungan[] = $transaksi + $awal;
        }

        $penarikanData = TransaksiTabungan::join('tabungans', 'transaksi_tabungans.tabungan_id', '=', 'tabungans.id')
            ->where('transaksi_tabungans.jenis_transaksi', 'Penarikan')
            ->where('tabungans.status', 'Aktif')
            ->selectRaw('MONTH(transaksi_tabungans.created_at) as month, SUM(transaksi_tabungans.jumlah) as total')
            ->groupBy(DB::raw('MONTH(transaksi_tabungans.created_at)'))
            ->pluck('total', 'month');

        $penarikanDataFormatted = [];
        for ($i = 1; $i <= 12; $i++) {
            $penarikanDataFormatted[] = $penarikanData->get($i, 0);
        }

        $labels = range(1, 12);

        return view('yayasan.dashboard', compact(
            'totalPemasukan',
            'totalPengeluaran',
            'total',
            'siswaAktif',
            'siswaNonAktif',
            'totalSiswa',
            'totalTabunganMasuk',
            'totalTabunganKeluar',
            'totalTabunganAkhir',
            'totalKasMasuk',
            'totalKasKeluar',
            'totalKas',
            'totalSaldoMasuk',
            'totalSaldoKeluar',
            'totalSaldoAkhir',
            'totalUnit',
            'keuanganPerUnit',
            'siswaPerUnit',
            'labels',
            'setoranGabungan',
            'penarikanDataFormatted'
        ));
    }
}