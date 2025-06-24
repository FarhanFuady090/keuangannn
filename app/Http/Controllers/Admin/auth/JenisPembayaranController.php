<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\JenisPembayaran;
use App\Models\TahunAjaran;
use Illuminate\Auth\Events\Registered;

class JenisPembayaranController extends Controller
{
    public function index(Request $request)
    {
        $unitId = $request->input('unitpendidikan');
        $status = $request->input('status');
        $perPage = $request->input('show', 10);

        $query = DB::table('jenispembayaran')
            ->join('unitpendidikan', 'jenispembayaran.idunitpendidikan', '=', 'unitpendidikan.id')
            ->join('tahunajaran', 'jenispembayaran.id_tahunajaran', '=', 'tahunajaran.id')
            ->select('jenispembayaran.*', 'unitpendidikan.namaUnit', 'tahunajaran.tahun_ajaran');

        if ($unitId) {
            $query->where('jenispembayaran.idunitpendidikan', '=', $unitId);
        }

        if ($status) {
            $query->where('jenispembayaran.status', '=', $status);
        }

        $filtered_data = $query->paginate($perPage)->withQueryString();
        $unitpendidikan = DB::table('unitpendidikan')->select('id', 'namaUnit')->get();

        return view('admin.manage-jenis-pembayaran', compact('filtered_data', 'unitpendidikan', 'unitId', 'status', 'perPage'));
    }

    public function create()
    {
        $tahunAjaran = TahunAjaran::where('status', 'Aktif')->get(['tahun_ajaran', 'id']);
        $unitpendidikan = DB::table('unitpendidikan')->get(['id', 'namaUnit']);

        return view('admin.create-jenis-pembayaran', compact('tahunAjaran', 'unitpendidikan'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_pembayaran' => 'required|string',
            'type' => 'required|string',
            'id_tahunajaran' => 'required|numeric',
            'nominal_jenispembayaran' => 'required|numeric',
            'status' => 'required|string',
            'idunitpendidikan' => 'required|numeric',
        ]);

        // Cek duplikasi
        $exists = JenisPembayaran::where([
            ['nama_pembayaran', $validated['nama_pembayaran']],
            ['type', $validated['type']],
            ['id_tahunajaran', $validated['id_tahunajaran']],
            ['nominal_jenispembayaran', $validated['nominal_jenispembayaran']],
            ['status', $validated['status']],
            ['idunitpendidikan', $validated['idunitpendidikan']],
        ])->exists();

        if ($exists) {
            return redirect()->back()->withErrors('Data sudah ada! Silakan masukkan data yang berbeda.');
        }

        $jenispembayaran = JenisPembayaran::create($validated);

        event(new Registered($jenispembayaran));

        return redirect()->route('admin.manage-jenis-pembayaran')->with('success', 'Data berhasil ditambahkan!');
    }

    public function editData($id)
    {
        $jenispembayaran = DB::table('jenispembayaran')
            ->join('unitpendidikan', 'jenispembayaran.idunitpendidikan', '=', 'unitpendidikan.id')
            ->join('tahunajaran', 'jenispembayaran.id_tahunajaran', '=', 'tahunajaran.id')
            ->select(
                'jenispembayaran.id',
                'jenispembayaran.nama_pembayaran',
                'jenispembayaran.type',
                'tahunajaran.tahun_ajaran',
                'jenispembayaran.nominal_jenispembayaran',
                'jenispembayaran.status',
                'unitpendidikan.namaUnit',
                'unitpendidikan.id as idunitpendidikan',
                'jenispembayaran.id_tahunajaran'
            )
            ->where('jenispembayaran.id', '=', $id)
            ->firstOrFail();

        $unitpendidikan = DB::table('unitpendidikan')->get(['id', 'namaUnit']);
        $tahunAjaran = TahunAjaran::where('status', 'Aktif')->get(['id', 'tahun_ajaran']);

        return view('admin.edit-jenis-pembayaran', compact('jenispembayaran', 'unitpendidikan', 'tahunAjaran'));
    }

    public function updateData(Request $request, $id)
    {
        $validated = $request->validate([
            'nama_pembayaran' => 'required|string',
            'type' => 'required|string',
            'id_tahunajaran' => 'required|numeric',
            'nominal_jenispembayaran' => 'required|numeric',
            'status' => 'required|string',
            'idunitpendidikan' => 'required|numeric',
        ]);

        DB::table('jenispembayaran')
            ->where('id', '=', $id)
            ->update([
                'nama_pembayaran' => $validated['nama_pembayaran'],
                'type' => $validated['type'],
                'id_tahunajaran' => $validated['id_tahunajaran'],
                'nominal_jenispembayaran' => $validated['nominal_jenispembayaran'],
                'status' => $validated['status'],
                'idunitpendidikan' => $validated['idunitpendidikan'],
            ]);

        return redirect()->route('admin.manage-jenis-pembayaran')->with('success', 'Data berhasil diperbarui!');
    }
}
