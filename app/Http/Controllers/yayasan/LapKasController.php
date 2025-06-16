<?php

namespace App\Http\Controllers\yayasan;

use App\Http\Controllers\Controller;
use App\Models\Kas;
use App\Models\TransaksiKas;
use App\Models\UnitPendidikan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
