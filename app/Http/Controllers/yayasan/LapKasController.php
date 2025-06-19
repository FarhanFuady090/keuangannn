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

        // Filter rentang waktu manual
        if ($request->filled('tanggal_awal') && $request->filled('tanggal_akhir')) {
            $query->whereBetween('created_at', [
                $request->tanggal_awal . ' 00:00:00',
                $request->tanggal_akhir . ' 23:59:59'
            ]);
        }

        // === Filter Tahun Ajaran dan Semester ===
        $tahunAjaran = $request->input('tahun_ajaran');
        $semester = $request->input('semester');
        $tahun = $request->input('tahun'); // Tahun biasa (misal: 2025)
        $bulan = $request->input('bulan'); // 1–12

        $start = null;
        $end = null;

        if ($tahun && $bulan) {
            $start = Carbon::create($tahun, $bulan, 1)->startOfMonth();
            $end = Carbon::create($tahun, $bulan, 1)->endOfMonth();
        } elseif ($tahunAjaran) {
            if ($semester === 'Ganjil') {
                $start = Carbon::create($tahunAjaran, 7, 1)->startOfDay();
                $end = Carbon::create($tahunAjaran, 12, 31)->endOfDay();
            } elseif ($semester === 'Genap') {
                $start = Carbon::create($tahunAjaran + 1, 1, 1)->startOfDay();
                $end = Carbon::create($tahunAjaran + 1, 6, 30)->endOfDay();
            } else {
                $start = Carbon::create($tahunAjaran, 7, 1)->startOfDay();
                $end = Carbon::create($tahunAjaran + 1, 6, 30)->endOfDay();
            }
        } elseif ($semester) {
            $nowYear = now()->year;
            if ($semester === 'Ganjil') {
                $start = Carbon::create($nowYear, 7, 1)->startOfDay();
                $end = Carbon::create($nowYear, 12, 31)->endOfDay();
            } elseif ($semester === 'Genap') {
                $start = Carbon::create($nowYear, 1, 1)->startOfDay();
                $end = Carbon::create($nowYear, 6, 30)->endOfDay();
            }
        } elseif ($tahun) {
            $start = Carbon::create($tahun, 1, 1)->startOfDay();
            $end = Carbon::create($tahun, 12, 31)->endOfDay();
        }

        if ($start && $end) {
            $query->whereBetween('created_at', [$start, $end]);
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