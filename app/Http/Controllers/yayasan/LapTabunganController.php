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

        $bulanInput = $request->input('bulan');       // ex: 1-12
        $tahunInput = $request->input('tahun');       // ex: 2025
        $semesterInput = $request->input('semester'); // 'Ganjil' or 'Genap'

        if ($bulanInput && !$tahunInput) {
            return back()->with('error', 'Silakan pilih tahun jika ingin memfilter berdasarkan bulan.');
        }


        $tanggalMulai = null;
        $tanggalSelesai = null;

        $tahun = $request->input('tahun');
        $bulan = $request->input('bulan');
        $tahunAjaran = $request->input('tahun_ajaran');
        $semester = $request->input('semester');

        // 1. Tahun + Bulan (Prioritas tertinggi)
        if ($tahun && $bulan) {
            $tanggalMulai = Carbon::create($tahun, $bulan, 1)->startOfMonth();
            $tanggalSelesai = Carbon::create($tahun, $bulan, 1)->endOfMonth();
        }

        // 2. Tahun Ajaran + (opsional) Semester
        elseif ($tahunAjaran) {
            if ($semester === 'Ganjil') {
                $tanggalMulai = Carbon::create($tahunAjaran, 7, 1)->startOfDay();
                $tanggalSelesai = Carbon::create($tahunAjaran, 12, 31)->endOfDay();
            } elseif ($semester === 'Genap') {
                $tanggalMulai = Carbon::create($tahunAjaran + 1, 1, 1)->startOfDay();
                $tanggalSelesai = Carbon::create($tahunAjaran + 1, 6, 30)->endOfDay();
            } else {
                // Jika tidak ada semester → tahun ajaran penuh
                $tanggalMulai = Carbon::create($tahunAjaran, 7, 1)->startOfDay();
                $tanggalSelesai = Carbon::create($tahunAjaran + 1, 6, 30)->endOfDay();
            }
        }

        // 3. Hanya semester (pakai tahun sekarang)
        elseif ($semester) {
            $now = now()->year;
            if ($semester === 'Ganjil') {
                $tanggalMulai = Carbon::create($now, 7, 1)->startOfDay();
                $tanggalSelesai = Carbon::create($now, 12, 31)->endOfDay();
            } elseif ($semester === 'Genap') {
                $tanggalMulai = Carbon::create($now, 1, 1)->startOfDay();
                $tanggalSelesai = Carbon::create($now, 6, 30)->endOfDay();
            }
        }

        // 4. Hanya tahun (misalnya: 2025 → Jan–Dec)
        elseif ($tahun) {
            $tanggalMulai = Carbon::create($tahun, 1, 1)->startOfDay();
            $tanggalSelesai = Carbon::create($tahun, 12, 31)->endOfDay();
        }

        // Terapkan ke query
        if ($tanggalMulai && $tanggalSelesai) {
            $query->whereBetween('created_at', [$tanggalMulai, $tanggalSelesai]);
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