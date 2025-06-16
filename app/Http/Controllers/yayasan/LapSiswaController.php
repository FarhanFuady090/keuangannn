<?php

namespace App\Http\Controllers\yayasan;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\UnitPendidikan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;


class LapSiswaController extends Controller
{

    public function ShowSiswa($id)
    {
        // Ambil data siswa berdasarkan ID
        $siswas = Siswa::findorFail($id);
        $unitpendidikan = UnitPendidikan::all();

        // Return view ke halaman detail data siswa
        return view('yayasan.laporan.siswa.show', compact('siswas', 'unitpendidikan'));
    }
    public function IndexSiswa(Request $request)
    {
        $query = Siswa::with('kelas', 'unitpendidikan');

        // Filter berdasarkan search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        // Filter berdasarkan status
        if ($request->filled('status') && $request->status !== 'Pilih Status') {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan kelas
        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        // Filter berdasarkan unitpendidikan
        if ($request->filled('unitpendidikan_id')) {
            $query->where('unitpendidikan_id', $request->unitpendidikan_id);
        }

        if ($request->filled('unitpendidikan_idInformal')) {
            $query->where('unitpendidikan_idInformal', $request->unitpendidikan_idInformal);
        }

        if ($request->filled('unitpendidikan_idPondok')) {
            $query->where('unitpendidikan_idPondok', $request->unitpendidikan_idPondok);
        }

        // Ambil jumlah entri yang ingin ditampilkan
        $perPage = $request->filled('entries') ? (int)$request->entries : 10;

        // Pagination dengan query string agar filter tidak hilang saat pindah halaman
        $siswas = $query->paginate($perPage)->withQueryString();

        // Ambil data kelas dan unit pendidikan
        $kelas = Kelas::all();
        $unitpendidikan = UnitPendidikan::all();
        $unitpendidikanformal = UnitPendidikan::where('kategori', 'Formal')->get();
        $unitpendidikaninformal = UnitPendidikan::where('kategori', 'Informal')->get();
        $unitpendidikanpondok = UnitPendidikan::where('kategori', 'Pondok')->get();

        return view('yayasan.laporan.siswa.index', compact(
            'siswas',
            'kelas',
            'unitpendidikan',
            'unitpendidikanformal',
            'unitpendidikaninformal',
            'unitpendidikanpondok'
        ));
    }
}
