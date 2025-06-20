<?php

namespace App\Http\Controllers\Tupusat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TahunAjaran;
use App\Models\UnitPendidikan;
use App\Models\JenisPembayaran;
use App\Models\Siswa;
use App\Models\Tagihan;
use App\Models\Kelas;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TagihanExport; // This will be the custom export class
use App\Exports\TagihanSiswaExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;

class TagihanController extends Controller
{
    // Daftar Siswa
    public function index(Request $request)
    {
        $unit   = $request->get('unit');
        $kelas  = $request->get('kelas');
        $search = $request->get('search');

        $query = Siswa::with('unitPendidikan', 'kelas');

        if ($unit) {
            $query->where('unitpendidikan_id', $unit);
        }

        if ($kelas) {
            $query->where('kelas_id', $kelas);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        $siswas = $query->paginate(15)->withQueryString();

        $unitpendidikan = UnitPendidikan::all();

        $kelasList = $unit ? Kelas::where('unitpendidikan_id', $unit)->get() : collect();

        return view('tupusat.tagihan.index', compact('siswas', 'unitpendidikan', 'kelasList', 'unit', 'kelas', 'search'));
    }

    public function create(Request $request)
    {
        $unitpendidikan = UnitPendidikan::all();
        $tahunAjaran = TahunAjaran::orderBy('tahun_ajaran', 'desc')->get();

        $selectedUnit = $request->get('unit');
        $selectedTahun = $request->get('tahun');
        $selectedKelas = $request->get('kelas');

        $jenisPembayaran = [];
        $siswaList = [];
        $kelasList = collect();

        if ($selectedUnit) {
            $kelasList = Kelas::where('unitpendidikan_id', $selectedUnit)->get();
        }

        if ($selectedUnit && $selectedTahun) {
            $jenisPembayaran = JenisPembayaran::where('idunitpendidikan', $selectedUnit)
                ->where('id_tahunajaran', $selectedTahun)
                ->where('status', 'Aktif')
                ->get();

            $query = Siswa::where('unitpendidikan_id', $selectedUnit)
                ->where('status', 'Aktif');

            if ($selectedKelas) {
                $query->where('kelas_id', $selectedKelas);
            }

            $siswaList = $query->get();
        }

        return view('tupusat.tagihan.create', compact(
            'unitpendidikan',
            'tahunAjaran',
            'jenisPembayaran',
            'siswaList',
            'kelasList',
            'selectedUnit',
            'selectedTahun',
            'selectedKelas'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis_pembayaran_id' => 'required|exists:jenispembayaran,id',
            'tahun_ajaran_id' => 'required|exists:tahunajaran,id',
            'siswa_ids' => 'required|array|min:1', // Pastikan ada minimal 1 siswa yang dipilih
            'siswa_ids.*' => 'exists:siswas,id',  // Validasi setiap siswa ID yang dipilih
        ]);

        $jenisPembayaran = JenisPembayaran::findOrFail($request->jenis_pembayaran_id);
        $type = $jenisPembayaran->type;

        $bulanMap = [
            'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember'
        ];

        $nominal = $jenisPembayaran->nominal_jenispembayaran; // nominal pembayaran

        // Proses tagihan hanya untuk siswa yang dipilih
        foreach ($request->siswa_ids as $siswaId) {
            foreach ($request->tagihan as $siswaTagihanId => $inputNominal) {
                if ($siswaId == $siswaTagihanId && $nominal > 0) {
                    switch ($type) {
                        case 'Bulanan':
                            foreach ($bulanMap as $bulan) {
                                Tagihan::create([
                                    'siswa_id' => $siswaId,
                                    'jenis_pembayaran_id' => $jenisPembayaran->id,
                                    'tahun_ajaran_id' => $request->tahun_ajaran_id,
                                    'bulan' => $bulan,
                                    'nominal' => $nominal,
                                    'jumlah_dibayar' => 0,
                                    'status' => 'belum'
                                ]);
                            }
                            break;

                        case 'Semester':
                            foreach (['Semester 1', 'Semester 2'] as $semesterBulan) {
                                Tagihan::create([
                                    'siswa_id' => $siswaId,
                                    'jenis_pembayaran_id' => $jenisPembayaran->id,
                                    'tahun_ajaran_id' => $request->tahun_ajaran_id,
                                    'bulan' => $semesterBulan,
                                    'nominal' => $nominal,
                                    'jumlah_dibayar' => 0,
                                    'status' => 'belum'
                                ]);
                            }
                            break;

                        case 'Tahunan':
                        case 'Bebas':
                            Tagihan::create([
                                'siswa_id' => $siswaId,
                                'jenis_pembayaran_id' => $jenisPembayaran->id,
                                'tahun_ajaran_id' => $request->tahun_ajaran_id,
                                'bulan' => null,
                                'nominal' => $nominal,
                                'jumlah_dibayar' => 0,
                                'status' => 'belum'
                            ]);
                            break;
                    }
                }
            }
        }

        return redirect()->route('tupusat.tagihan.create')->with('success', 'Tagihan berhasil dibuat.');
    }

    // Rincian Tagihan Siswa
    public function show(Request $request, $siswaId)
    {
        $siswa = Siswa::findOrFail($siswaId);
        $unitPendidikanId = $siswa->unitpendidikan_id;  // Mengambil ID unit pendidikan siswa

        // Menyesuaikan jenis pembayaran berdasarkan unit pendidikan
        $jenisPembayaranList = JenisPembayaran::where('idunitpendidikan', $unitPendidikanId)->get();

        // Query untuk tagihan siswa
        $query = Tagihan::where('siswa_id', $siswaId);

        // Filter berdasarkan jenis pembayaran
        if ($request->has('jenis_pembayaran') && $request->jenis_pembayaran) {
            $query->where('jenis_pembayaran_id', $request->jenis_pembayaran);
        }

        // Filter berdasarkan status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $perPage = $request->input('perPage', 15);
        $tagihans = $query->paginate($perPage)->withQueryString();

        return view('tupusat.tagihan.show', compact('siswa', 'tagihans', 'jenisPembayaranList'));
    }

    // Form bayar tagihan
    public function formBayar(Tagihan $tagihan)
    {
        return view('tupusat.tagihan.bayar', compact('tagihan'));
    }

    // Proses pembayaran tagihan
    public function bayar(Request $request, Tagihan $tagihan)
    {
        $request->validate([
            'jumlah_bayar' => 'required|numeric|min:1|max:' . ($tagihan->nominal - $tagihan->jumlah_dibayar),
            'tanggal_bayar' => 'required|date',
        ]);

        // Update tagihan jumlah_dibayar dan status
        $tagihan->jumlah_dibayar += $request->jumlah_bayar;
        if ($tagihan->jumlah_dibayar >= $tagihan->nominal) {
            $tagihan->status = 'lunas';
            $tagihan->tanggal_bayar = $request->tanggal_bayar;
        }
        $tagihan->save();

        return redirect()->route('tupusat.tagihan.show', $tagihan->siswa_id)
            ->with('success', 'Pembayaran berhasil disimpan.');
    }

    public function getJenisPembayaran(Request $request)
    {
        $unitId = $request->query('unit_id');
        $tahunId = $request->query('tahun_id');

        $data = JenisPembayaran::where('idunitpendidikan', $unitId)
            ->where('id_tahunajaran', $tahunId)
            ->where('status', 'Aktif')
            ->get(['id', 'nama_pembayaran', 'type']);

        return response()->json($data);
    }

    public function getSiswa(Request $request)
    {
        $unitId = $request->query('unit_id');
        $kelasId = $request->query('kelas_id');

        $query = Siswa::where('unitpendidikan_id', $unitId)
            ->where('status', 'Aktif');

        if ($kelasId) {
            $query->where('kelas_id', $kelasId);
        }

        $data = $query->get(['id', 'nama', 'nis', 'kelas_id']);

        return response()->json($data);
    }

    public function cetak(Request $request, Siswa $siswa)
    {
        $jenisPembayaranFilter = $request->get('jenis_pembayaran');

        $query = Tagihan::with('jenisPembayaran', 'tahunAjaran')
            ->where('siswa_id', $siswa->id);

        if ($jenisPembayaranFilter) {
            $query->where('jenis_pembayaran_id', $jenisPembayaranFilter);
        }

        $tagihans = $query->get();

        $totalTagihan = $tagihans->sum('nominal');
        $totalDibayar = $tagihans->sum('jumlah_dibayar');
        $sisaTagihan = $totalTagihan - $totalDibayar;

        $pdf = Pdf::loadView('tupusat.tagihan.cetak', compact('siswa', 'tagihans', 'totalTagihan', 'totalDibayar', 'sisaTagihan'))
            ->setPaper('A4', 'portrait');

        return $pdf->stream("Kwitansi_{$siswa->nama}.pdf");
    }

    public function getKelasByUnit(Request $request)
    {
        $unitId = $request->get('unit_id');

        if (!$unitId) {
            return response()->json([]);
        }

        $kelas = Kelas::where('unitpendidikan_id', $unitId)->get(['id', 'nama_kelas']);

        return response()->json($kelas);
    }

    public function getNominalJenisPembayaran(Request $request)
    {
        $id = $request->get('id');
        $jenis = JenisPembayaran::find($id);

        if (!$jenis) {
            return response()->json(['error' => 'Jenis tidak ditemukan'], 404);
        }

        return response()->json(['nominal' => $jenis->nominal_jenispembayaran]);
    }

    public function cetakKwitansi(Tagihan $tagihan)
    {
        if ($tagihan->status !== 'lunas') {
            abort(403, 'Tagihan belum lunas');
        }

        $siswa = $tagihan->siswa; // pastikan relasi siswa ada
        $jenisPembayaran = $tagihan->jenisPembayaran;
        $tahunAjaran = $tagihan->tahunAjaran;

        $pdf = Pdf::loadView('tupusat.tagihan.kwitansi', compact('tagihan', 'siswa', 'jenisPembayaran', 'tahunAjaran'))
            ->setPaper('A5', 'landscape');

        return $pdf->stream("Kwitansi_{$siswa->nama}_{$tagihan->id}.pdf");
    }

    public function cetakMultipleKwitansi(Request $request)
    {
        $ids = $request->input('tagihan_ids', []);

        if (empty($ids)) {
            return redirect()->back()->with('error', 'Tidak ada tagihan yang dipilih.');
        }

        $tagihans = Tagihan::with(['siswa', 'jenisPembayaran', 'tahunAjaran'])
            ->whereIn('id', $ids)
            ->where('status', 'lunas')
            ->get();

        if ($tagihans->isEmpty()) {
            return redirect()->back()->with('error', 'Tagihan tidak valid atau belum lunas.');
        }

        $pdf = Pdf::loadView('tupusat.tagihan.kwitansi-multiple', compact('tagihans'))
            ->setPaper('A5', 'portrait');

        return $pdf->stream('kwitansi-multiple.pdf');
    }

    public function exportExcel(Request $request, $siswaId)
    {
        // Filter data yang ingin diexport, seperti di method show sebelumnya
        $siswa = Siswa::findOrFail($siswaId);
        $tagihans = Tagihan::where('siswa_id', $siswaId)
            ->with('jenisPembayaran', 'tahunAjaran')
            ->get();

        return Excel::download(new TagihanExport($tagihans), 'tagihan_siswa.xlsx');
    }
    public function exportAll(Request $request)
    {
        $unit = $request->get('unit');
        $kelas = $request->get('kelas');

        $query = Siswa::with(['kelas', 'unitPendidikan', 'tagihan']);

        if ($unit) {
            $query->where('unitpendidikan_id', $unit);
        }

        if ($kelas) {
            $query->where('kelas_id', $kelas);
        }

        $siswas = $query->get();

        return Excel::download(new TagihanSiswaExport($siswas), 'daftar_tagihan_siswa.xlsx');
    }
}