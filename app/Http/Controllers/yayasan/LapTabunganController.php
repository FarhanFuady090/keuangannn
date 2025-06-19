<?php

namespace App\Http\Controllers\yayasan;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Siswa;
use App\Models\Tabungan;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\TabunganExport;
use App\Exports\AllTabunganExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\TahunAjaran;
use Carbon\Carbon;

class LapTabunganController extends Controller
{
    // Menampilkan semua tabungan (bisa difilter berdasarkan unit)
    public function index(Request $request)
    {
        $query = Tabungan::with(['siswa.kelas.unitpendidikan']); // Eager load relasi siswa -> kelas -> unit

        // Filter soft deleted
        if ($request->has('trashed') && $request->trashed == true) {
            $query->onlyTrashed();
        }

        // Filter berdasarkan nama siswa
        if ($request->has('search') && $request->search != '') {
            $query->whereHas('siswa', function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->search . '%');
            });
        }

        // 🔁 Ubah filter status agar berdasarkan status siswa
        if ($request->has('status') && $request->status != '') {
            $query->whereHas('siswa', function ($q) use ($request) {
                $q->where('status', $request->status);
            });
        }

        // Filter berdasarkan unit pendidikan
        if ($request->has('unit') && $request->unit != '') {
            $query->whereHas('siswa.kelas.unitpendidikan', function ($q) use ($request) {
                $q->where('id', $request->unit);
            });
        }

        // Filter berdasarkan kelas
        if ($request->has('kelas') && $request->kelas != '') {
            $query->whereHas('siswa.kelas', function ($q) use ($request) {
                $q->where('id', $request->kelas);
            });
        }

        // Filter rentang waktu created_at
        if ($request->filled('tanggal_awal') && $request->filled('tanggal_akhir')) {
            $query->whereBetween('created_at', [
                $request->tanggal_awal . ' 00:00:00',
                $request->tanggal_akhir . ' 23:59:59'
            ]);
        }


        if ($request->filled('tahun_ajaran') || $request->filled('semester')) {
            $tahunInput = $request->input('tahun_ajaran'); // contoh: 2025
            $semesterInput = $request->input('semester'); // Ganjil / Genap

            // Jika tahun dipilih → tahun ajaran: Juli tahun itu sampai Juni tahun +1
            if ($tahunInput) {
                $start = Carbon::create($tahunInput, 7, 1)->startOfDay(); // 1 Juli tahun itu
                $end = Carbon::create($tahunInput + 1, 6, 30)->endOfDay(); // 30 Juni tahun berikutnya

                // Jika semester juga dipilih, sesuaikan
                if ($semesterInput === 'Ganjil') {
                    $start = Carbon::create($tahunInput, 7, 1)->startOfDay();
                    $end = Carbon::create($tahunInput, 12, 31)->endOfDay();
                } elseif ($semesterInput === 'Genap') {
                    $start = Carbon::create($tahunInput + 1, 1, 1)->startOfDay();
                    $end = Carbon::create($tahunInput + 1, 6, 30)->endOfDay();
                }
            }
            // Jika hanya semester saja yang dipilih, tahun default = sekarang
            elseif ($semesterInput) {
                $nowYear = now()->year;
                if ($semesterInput === 'Ganjil') {
                    $start = Carbon::create($nowYear, 7, 1)->startOfDay();
                    $end = Carbon::create($nowYear, 12, 31)->endOfDay();
                } elseif ($semesterInput === 'Genap') {
                    $start = Carbon::create($nowYear, 1, 1)->startOfDay();
                    $end = Carbon::create($nowYear, 6, 30)->endOfDay();
                }
            }

            // Terapkan filter waktu
            if (isset($start) && isset($end)) {
                $query->whereBetween('created_at', [$start, $end]);
            }
        }

        // Ambil data dan paginate
        $tabungans = $query->paginate(20);

        // Loop tabungan untuk hitung total setoran & penarikan
        foreach ($tabungans as $tabungan) {
            $setoranAwal = $tabungan->saldo_awal;

            $totalSetoranTransaksi = $tabungan->transaksi()
                ->where('jenis_transaksi', 'Setoran')
                ->sum('jumlah');

            $totalPenarikan = $tabungan->transaksi()
                ->where('jenis_transaksi', 'Penarikan')
                ->sum('jumlah');

            // Tambahkan ke properti virtual
            $tabungan->total_setoran = $setoranAwal + $totalSetoranTransaksi;
            $tabungan->total_penarikan = $totalPenarikan;
        }

        // Data dropdown
        $units = \App\Models\UnitPendidikan::all();
        $kelasList = \App\Models\Kelas::all();

        return view('yayasan.laporan.tabungan.index', compact('tabungans', 'units', 'kelasList'));
    }


    // Detail tabungan per siswa
    public function show($id, Request $request)
    {
        $tabungan = Tabungan::with('siswa')->findOrFail($id);

        $transaksiQuery = $tabungan->transaksi()->orderBy('created_at', 'asc');

        if ($request->filled('start') && $request->filled('end')) {
            $transaksiQuery->whereBetween('created_at', [$request->start, $request->end]);
        }

        $transaksi = $transaksiQuery->get();

        $transaksiPerBulan = $tabungan->transaksi()
            ->selectRaw("
        DATE_FORMAT(created_at, '%Y-%m') as bulan,
        SUM(CASE WHEN jenis_transaksi = 'Setoran' THEN jumlah ELSE 0 END) as total_setoran,
        SUM(CASE WHEN jenis_transaksi = 'Penarikan' THEN jumlah ELSE 0 END) as total_penarikan
    ")
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get()
            ->keyBy('bulan');

        // Ambil bulan saat tabungan dibuat
        $createdMonth = $tabungan->created_at->format('Y-m');

        // Jika bulan saldo_awal sama dengan salah satu bulan transaksi, tambahkan ke total_setoran
        if ($transaksiPerBulan->has($createdMonth)) {
            $transaksiPerBulan[$createdMonth]->total_setoran += $tabungan->saldo_awal;
        } else {
            // Jika tidak ada transaksi di bulan tersebut, buat entri baru
            $transaksiPerBulan->put($createdMonth, (object)[
                'bulan' => $createdMonth,
                'total_setoran' => $tabungan->saldo_awal,
                'total_penarikan' => 0,
            ]);
        }

        // Urutkan ulang berdasarkan bulan dan reset key
        $chartData = $transaksiPerBulan->sortKeys()->values();


        return view('yayasan.laporan.tabungan.show', compact('tabungan', 'transaksi', 'chartData'));
    }

    public function exportPdf($id)
    {
        $tabungan = Tabungan::with(['siswa', 'transaksi'])->findOrFail($id);
        $pdf = Pdf::loadView('yayasan.laporan.tabungan.export_pdf', compact('tabungan'));
        return $pdf->download('Laporan_Tabungan_' . $tabungan->siswa->nama . '.pdf');
    }

    public function exportAll()
    {
        return Excel::download(new AllTabunganExport, 'rekap_semua_tabungan.xlsx');
    }
}
