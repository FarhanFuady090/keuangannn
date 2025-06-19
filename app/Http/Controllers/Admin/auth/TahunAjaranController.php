<?php

namespace App\Http\Controllers\Admin\auth;

use App\Http\Controllers\Controller;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TahunAjaranController extends Controller
{
    function createTahunAjaran()
    {
        return view("admin.create-tahun-ajaran");
    }

    function submitTahunAjaran(Request $request)
    {
        // Validasi input
        $request->validate([
            'tahun_ajaran' => 'required|string',
            'status' => 'required|in:Aktif,Non Aktif',
        ]);

        $tahunajaran = new TahunAjaran();
        $tahunajaran->tahun_ajaran = $request->tahun_ajaran;
        $tahunajaran->awal = $request->awal;
        $tahunajaran->akhir = $request->akhir;
        $tahunajaran->status = $request->status;

        // Cek Tahun Awal apakah sudah ada
        if (TahunAjaran::where('awal', $request->awal)->exists()) {
            return redirect()->back()->withErrors(['awal' => 'Tahun Awal telah digunakan.'])->withInput();
        }
        $tahunajaran->save();

        return redirect()->route('admin.manage-tahun-ajaran')->with('success', 'Tahun ajaran berhasil ditambah.');
    }

    function editTahunAjaran($id)
    {
        $tahunajaran = TahunAjaran::find($id);
        return view('admin.edit-tahun-ajaran', compact('tahunajaran'));
    }

    function updateTahunAjaran(Request $request, $id)
    {
        $tahunajaran = TahunAjaran::findOrFail($id);

        // Validasi input
        $request->validate([
            'tahun_ajaran' => 'required|string',
            'status' => 'required|in:Aktif,Non Aktif',
        ]);

        $tahunajaran->tahun_ajaran = $request->tahun_ajaran;
        $tahunajaran->awal = $request->awal;
        $tahunajaran->akhir = $request->akhir;
        $tahunajaran->status = $request->status;

        // Cek Tahun Awal
        if (TahunAjaran::where('awal', $request->awal)->where('id', '!=', $id)->exists()) {
            return redirect()->back()->withErrors(['awal' => 'Tahun Awal telah digunakan.'])->withInput();
        }

        $tahunajaran->update();

        return redirect()->route('admin.manage-tahun-ajaran')->with('success', 'Tahun ajaran berhasil diperbarui.');
    }

    public function index(Request $request)
    {
        $query = TahunAjaran::query();

        // Ambil filter status dari request (default: "Semua")
        $status = $request->input('status', 'Semua');
        if ($status !== 'Semua') {
            $query->where('status', $status);
        }

        // Ambil filter pencarian dari request
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('tahun_ajaran', 'LIKE', "%$search%");
        }

        // Ambil jumlah entri dari request (default: 10)
        $perPage = $request->input('entries', 10);

        // Ambil data sesuai filter yang dipilih dengan pagination
        $tahunajaran = $query->paginate($perPage);

        // Kirim filter yang dipilih ke view agar tetap tersimpan
        return view('admin.manage-tahun-ajaran', compact('tahunajaran', 'perPage', 'status'));
    }
}
