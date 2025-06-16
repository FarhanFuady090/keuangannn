<?php

namespace App\Http\Controllers\tupusat;

use App\Http\Controllers\Controller;
use App\Models\Kas;
use App\Models\TransaksiKas;
use App\Models\UnitPendidikan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KasController extends Controller
{
    // Menampilkan data kas dan transaksi
    public function index()
    {
        // Mengambil semua transaksi kas dengan eager load relasi kas dan unitPendidikan
        $transaksiKas = TransaksiKas::with('kas', 'unitpendidikan')->get(); // Tampilkan semua transaksi
        $kas = Kas::where('status', 'Aktif')->get();

        return view('tupusat.kas.index', compact('kas', 'transaksiKas'));
    }

    // Form untuk tambah transaksi kas
    public function create()
    {
        $kas = Kas::where('status', 'Aktif')->get();
        $unitpendidikan = UnitPendidikan::all(); // Ambil data unit pendidikan
        return view('tupusat.kas.create', compact('kas', 'unitpendidikan'));
    }

    // Proses tambah transaksi kas
    public function store(Request $request)
    {
        $request->validate([
            'tanggal_bayar' => 'required|date',
            'kas_id' => 'required|exists:kas,id',
            'nominal' => 'required|numeric|min:1',
            'unitpendidikan_id' => 'required|exists:unitpendidikan,id',
            'keterangan' => 'nullable|string|max:255',
            'created_by' => 'required|string',
        ]);

        TransaksiKas::create([
            'tanggal_bayar' => $request->tanggal_bayar,
            'kas_id' => $request->kas_id,
            'nominal' => $request->nominal,
            'unitpendidikan_id' => $request->unitpendidikan_id,
            'keterangan' => $request->keterangan,
            'created_by' => $request->created_by
        ]);

        return redirect()->route('tupusat.kas.index')->with('success', 'Transaksi kas berhasil ditambahkan');
    }

    // Edit transaksi kas
    public function edit($id)
    {
        $transaksiKas = TransaksiKas::findOrFail($id);
        $kas = Kas::where('status', 'Aktif')->get();
        $unitPendidikan = UnitPendidikan::all();

        return view('tupusat.kas.edit', compact('transaksiKas', 'kas', 'unitPendidikan'));
    }

    // Update transaksi kas
    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal_bayar' => 'required|date',
            'kas_id' => 'required|exists:kas,id',
            'nominal' => 'required|numeric|min:1',
            'unitpendidikan_id' => 'required|exists:unitpendidikan,id',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $transaksiKas = TransaksiKas::findOrFail($id);
        $transaksiKas->update([
            'tanggal_bayar' => $request->tanggal_bayar,
            'kas_id' => $request->kas_id,
            'nominal' => $request->nominal,
            'unitpendidikan_id' => $request->unitpendidikan_id,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('tupusat.kas.index')->with('success', 'Transaksi kas berhasil diperbarui.');
    }

    // Hapus transaksi kas
    public function destroy($id)
    {
        $transaksiKas = TransaksiKas::findOrFail($id);
        $transaksiKas->deleted_by = Auth::user()->username; // Atau ->name / ->email tergantung kolom di User
        $transaksiKas->save();
        // Lakukan soft delete
        $transaksiKas->delete();


        return redirect()->route('tupusat.kas.index')->with('success', 'Transaksi kas berhasil dihapus.');
    }

    public function trashed()
    {
        $trashedTransaksiKas = TransaksiKas::onlyTrashed()->with(['kas', 'unitpendidikan'])->get();

        return view('tupusat.kas.trashed', compact('trashedTransaksiKas'));
    }

    public function restore($id)
    {
        $transaksi = TransaksiKas::onlyTrashed()->findOrFail($id);
        $transaksi->restore();

        return redirect()->route('tupusat.kas.index')->with('success', 'Transaksi berhasil direstore.');
    }

    public function forceDelete($id)
    {
        $transaksi = TransaksiKas::onlyTrashed()->findOrFail($id);
        $transaksi->forceDelete();

        return redirect()->route('tupusat.kas.trashed')->with('success', 'Transaksi berhasil dihapus permanen.');
    }
}