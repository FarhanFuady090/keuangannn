<?php

namespace App\Http\Controllers\tupusat;

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

class TabunganController extends Controller
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

        return view('tupusat.tabungan.index', compact('tabungans', 'units', 'kelasList'));
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


        return view('tupusat.tabungan.show', compact('tabungan', 'transaksi', 'chartData'));
    }

    // Membuat tabungan baru untuk siswa (jika belum punya)
    public function create()
    {
        $siswas = Siswa::where('status', 'Aktif')
            ->whereDoesntHave('tabungan', function ($query) {
                $query->onlyTrashed();
            })
            ->whereDoesntHave('tabungan') // pastikan tidak punya tabungan aktif juga
            ->get();

        return view('tupusat.tabungan.create', compact('siswas'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswas,id',
            'saldo_awal' => 'required|numeric|min:0',
            'updated_by' => 'nullable|string',
        ]);

        // Cek apakah siswa sudah punya tabungan, termasuk yang sudah di-soft delete
        $existingTabungan = Tabungan::withTrashed()->where('siswa_id', $request->siswa_id)->first();

        if ($existingTabungan) {
            return back()->withErrors(['siswa_id' => 'Siswa ini sudah memiliki tabungan, termasuk yang sudah terhapus.'])->withInput();
        }

        // Simpan data baru
        Tabungan::create([
            'siswa_id' => $request->siswa_id,
            'saldo_awal' => $request->saldo_awal,
            'status' => 'Aktif',
            'created_by' => Auth::user()->username,
            // 'updated_by' => Auth::user()->username,
        ]);


        return redirect()->route('tupusat.tabungan.index')->with('success', 'Tabungan berhasil dibuat.');
    }

    public function edit($id)
    {
        $tabungan = Tabungan::with('siswa')->findOrFail($id);
        return view('tupusat.tabungan.edit', compact('tabungan'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'saldo_awal' => 'required|numeric',
            'updated_by' => 'nullable|string',
            'information' => 'nullable|string',
        ]);

        $tabungan = Tabungan::findOrFail($id);
        $tabungan->update($request->all());

        return redirect()->route('tupusat.tabungan.index', $tabungan->tabungan_id)->with('success', 'Tabungan berhasil diperbarui.');
    }


    public function exportPdf($id)
    {
        $tabungan = Tabungan::with(['siswa', 'transaksi'])->findOrFail($id);
        $pdf = Pdf::loadView('tupusat.tabungan.export_pdf', compact('tabungan'));
        return $pdf->download('Laporan_Tabungan_' . $tabungan->siswa->nama . '.pdf');
    }

    public function exportAll()
    {
        return Excel::download(new AllTabunganExport, 'rekap_semua_tabungan.xlsx');
    }

    public function destroy($id)
    {
        $tabungan = Tabungan::findOrFail($id);

        $tabungan->deleted_by = Auth::user()->username; // Atau ->name / ->email tergantung kolom di User
        $tabungan->status = 'Non Aktif';
        $tabungan->save();

        // Lakukan soft delete
        $tabungan->delete();

        return back()->with('success', 'Tabungan berhasil dihapus dan status diubah menjadi Non Aktif.');
    }


    public function restore($id)
    {
        $tabungan = Tabungan::onlyTrashed()->findOrFail($id);
        $tabungan->status = 'Aktif';
        $tabungan->restore();

        return back()->with('success', 'Data tabungan berhasil dipulihkan.');
    }

    public function forceDelete($id)
    {
        $transaksi = Tabungan::onlyTrashed()->findOrFail($id);
        $transaksi->forceDelete();

        return back()->with('success', 'Data tabungan berhasil dihapus permanen.');
    }
}