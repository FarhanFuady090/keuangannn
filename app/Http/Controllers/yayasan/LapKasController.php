<?php

namespace App\Http\Controllers\yayasan;

use App\Http\Controllers\Controller;
use App\Models\Kas;
use App\Models\TransaksiKas;
use App\Models\UnitPendidikan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\TahunAjaran;
use Carbon\Carbon;

class LapKasController extends Controller
{
    // Menampilkan data kas dan transaksi
    public function index(Request $request)
    {
        $query = TransaksiKas::with(['kas', 'unitpendidikan']);

        if ($request->filled('kas')) {
            $query->where('kas_id', $request->kas);
        }

        if ($request->filled('unit_pendidikan')) {
            $query->where('unitpendidikan_id', $request->unit_pendidikan);
        }

        if ($request->filled('created_by')) {
            $query->where('created_by', $request->created_by);
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

        $transaksiKas = $query->get();

        $filterKas = TransaksiKas::select('kas_id')
            ->distinct()
            ->with('kas:id,namaKas')
            ->get()
            ->pluck('kas')
            ->filter();

        $unitPendidikanFilter = TransaksiKas::select('unitpendidikan_id')
            ->distinct()
            ->with('unitpendidikan:id,namaunit')
            ->get()
            ->pluck('unitpendidikan')
            ->filter();

        $createdByUsers = TransaksiKas::select('created_by')->distinct()->pluck('created_by');

        $kas = Kas::where('status', 'Aktif')->get();

        return view('yayasan.laporan.kas.index', compact(
            'transaksiKas',
            'filterKas',
            'unitPendidikanFilter',
            'createdByUsers',
            'kas'
        ));
    }


    public function trashed()
    {
        $trashedTransaksiKas = TransaksiKas::onlyTrashed()->with(['kas', 'unitpendidikan'])->get();

        return view('yayasan.laporan.kas.trashed', compact('trashedTransaksiKas'));
    }
}