<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<x-layout-yayasan>
    <x-slot name="header">

    </x-slot>
    <div class="mx-auto mt-10 px-6" style="max-width: 1200px">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">

            <!-- Statistik Total Uang Masuk -->
            <div class="p-6 bg-white border-l-4 border-green-500 rounded-lg shadow-md">
                <h3 class="text-xl font-semibold text-gray-800">Total Pemasukan</h3>
                <p class="text-3xl font-bold text-green-700">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</p>
            </div>

            <!-- Statistik Total Uang Keluar -->
            <div class="p-6 bg-white border-l-4 border-red-500 rounded-lg shadow-md">
                <h3 class="text-xl font-semibold text-gray-800">Total Pengeluaran</h3>
                <p class="text-3xl font-bold text-red-700">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</p>
            </div>

            <!-- Statistik Total Uang-->
            <div class="p-6 bg-white border-l-4 border-blue-500 rounded-lg shadow-md">
                <h3 class="text-xl font-semibold text-gray-800">Total Akhir</h3>
                <p class="text-3xl font-bold text-blue-700">Rp {{ number_format($total, 0, ',', '.') }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">

            <!-- Statistik Total Uang Masuk -->
            <div class="p-6 bg-white border-l-4 border-green-500 rounded-lg shadow-md">
                <h3 class="text-xl font-semibold text-gray-800">Total Pemasukan Tabungan</h3>
                <p class="text-3xl font-bold text-green-700">Rp {{ number_format($totalTabunganMasuk, 0, ',', '.') }}
                </p>
            </div>

            <!-- Statistik Total Uang Keluar -->
            <div class="p-6 bg-white border-l-4 border-red-500 rounded-lg shadow-md">
                <h3 class="text-xl font-semibold text-gray-800">Total Pengeluaran Tabungan</h3>
                <p class="text-3xl font-bold text-red-700">Rp {{ number_format($totalTabunganKeluar, 0, ',', '.') }}</p>
            </div>

            <!-- Statistik Total Uang-->
            <div class="p-6 bg-white border-l-4 border-blue-500 rounded-lg shadow-md">
                <ungan class="text-xl font-semibold text-gray-800">Total Akhir Tabungan</ungan>
                <p class="text-3xl font-bold text-blue-700">Rp {{ number_format($totalTabunganAkhir, 0, ',', '.') }}</p>
            </div>
        </div>

        <!-- Statistik Keuangan per Unit Pendidikan -->
        <div class="bg-white p-6 rounded-lg shadow-md mt-6 mb-6">
        {{-- <div class="flex gap-4 my-4">
            <a href="{{ route('keuangan.export.excel') }}" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Export Excel</a>
            <a href="{{ route('keuangan.export.pdf') }}" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Export PDF</a>
        </div> --}}

            <h3 class="text-xl font-semibold text-gray-800 mb-4">Distribusi Keuangan per Unit Pendidikan</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-200 text-sm rounded-lg shadow-sm">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="py-3 px-4 border-b">Unit Pendidikan</th>
                            <th class="py-3 px-4 border-b">Total Tabungan Masuk</th>
                            <th class="py-3 px-4 border-b">Total Tabungan Keluar</th>
                            <th class="py-3 px-4 border-b">Total Tabungan Akhir</th>
                            <th class="py-3 px-4 border-b">Total Kas Masuk</th>
                            <th class="py-3 px-4 border-b">Total Kas Keluar</th>
                            <th class="py-3 px-4 border-b">Total Kas</th>
                            <th class="py-3 px-4 border-b">Total Tagihan Terbayar</th>
                            <th class="py-3 px-4 border-b">Total Tagihan Belum Terbayar</th>
                            <th class="py-3 px-4 border-b">Total Tagihan</th>
                            <th class="py-3 px-4 border-b">Total Pemasukan</th>
                            <th class="py-3 px-4 border-b">Total Pengeluaran</th>
                            <th class="py-3 px-4 border-b">Total Akhir</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $unitNames = [
                        2 => 'TK',
                        3 => 'SD',
                        4 => 'SMP',
                        5 => 'SMA',
                        6 => 'MADIN',
                        7 => 'TPQ',
                        8 => 'PONDOK',
                        ];
                        @endphp

                        @if(isset($keuanganPerUnit) && count($keuanganPerUnit) > 0)
                        @foreach ($keuanganPerUnit as $data)
                        <tr>
                            <td class="border px-4 py-2">
                                {{ $unitNames[$data->unitpendidikan->id ?? null] ?? 'Unit Tidak Ditemukan' }}
                            </td>
                            <td class="border px-4 py-2 text-center text-green-700">Rp
                                {{ number_format($data->total_saldo_masuk, 0, ',', '.') }}
                            </td>
                            <td class="border px-4 py-2 text-center text-red-700">Rp
                                {{ number_format($data->total_saldo_keluar, 0, ',', '.') }}
                            </td>
                            <td class="border px-4 py-2 text-center font-semibold text-blue-700">Rp
                                {{ number_format($data->total_saldo_akhir, 0, ',', '.') }}
                            </td>
                            <td class="border px-4 py-2 text-center text-green-700">Rp
                                {{ number_format($data->total_kas_masuk, 0, ',', '.') }}
                            </td>
                            <td class="border px-4 py-2 text-center text-red-700">Rp
                                {{ number_format($data->total_kas_keluar, 0, ',', '.') }}
                            </td>
                            <td class="border px-4 py-2 text-center font-semibold text-blue-700">Rp
                                {{ number_format($data->total_kas, 0, ',', '.') }}
                            </td>
                            <td class="border px-4 py-2 text-center text-green-700">Rp
                                {{ number_format($data->total_tagihan_terbayar, 0, ',', '.') }}
                            </td>
                            <td class="border px-4 py-2 text-center text-red-700">Rp
                                {{ number_format($data->total_tagihan_belum_terbayar, 0, ',', '.') }}
                            </td>
                            <td class="border px-4 py-2 text-center font-semibold text-blue-700">Rp
                                {{ number_format($data->total_tagihan, 0, ',', '.') }}
                            </td>
                            <td class="border px-4 py-2 text-center font-semibold text-green-700">Rp
                                {{ number_format($data->total_pemasukan, 0, ',', '.') }}
                            </td>
                            <td class="border px-4 py-2 text-center font-semibold text-red-700">Rp
                                {{ number_format($data->total_pengeluaran, 0, ',', '.') }}
                            </td>
                            <td class="border px-4 py-2 text-center font-semibold text-blue-700">Rp
                                {{ number_format($data->total_akhir, 0, ',', '.') }}
                            </td>
                        </tr>
                        @endforeach
                        @else
                        <tr>
                            <td colspan="13" class="border px-4 py-6 text-center text-gray-500 italic">
                                Data tidak tersedia
                            </td>
                        </tr>
                        @endif
                    </tbody>
                    @if(isset($keuanganPerUnit) && count($keuanganPerUnit) > 0)
                    <tfoot class="bg-gray-50">
                        <tr>
                            <th class="border px-4 py-2 font-bold text-gray-800">Total Keseluruhan</th>
                            <th class="border px-4 py-2 text-center font-bold text-green-700">
                                Rp {{ number_format(collect($keuanganPerUnit)->sum('total_saldo_masuk'), 0, ',', '.') }}
                            </th>
                            <th class="border px-4 py-2 text-center font-bold text-red-700">
                                Rp
                                {{ number_format(collect($keuanganPerUnit)->sum('total_saldo_keluar'), 0, ',', '.') }}
                            </th>
                            <th class="border px-4 py-2 text-center font-bold text-blue-700">
                                Rp {{ number_format(collect($keuanganPerUnit)->sum('total_saldo_akhir'), 0, ',', '.') }}
                            </th>
                            <th class="border px-4 py-2 text-center font-bold text-green-700">
                                Rp {{ number_format(collect($keuanganPerUnit)->sum('total_kas_masuk'), 0, ',', '.') }}
                            </th>
                            <th class="border px-4 py-2 text-center font-bold text-red-700">
                                Rp {{ number_format(collect($keuanganPerUnit)->sum('total_kas_keluar'), 0, ',', '.') }}
                            </th>
                            <th class="border px-4 py-2 text-center font-bold text-blue-700">
                                Rp {{ number_format(collect($keuanganPerUnit)->sum('total_kas'), 0, ',', '.') }}
                            </th>
                            <th class="border px-4 py-2 text-center font-bold text-green-700">
                                Rp
                                {{ number_format(collect($keuanganPerUnit)->sum('total_tagihan_terbayar'), 0, ',', '.') }}
                            </th>
                            <th class="border px-4 py-2 text-center font-bold text-red-700">
                                Rp
                                {{ number_format(collect($keuanganPerUnit)->sum('total_tagihan_belum_terbayar'), 0, ',', '.') }}
                            </th>
                            <th class="border px-4 py-2 text-center font-bold text-blue-700">
                                Rp {{ number_format(collect($keuanganPerUnit)->sum('total_tagihan'), 0, ',', '.') }}
                            </th>
                            <th class="border px-4 py-2 text-center font-bold text-green-700">
                                Rp {{ number_format(collect($keuanganPerUnit)->sum('total_pemasukan'), 0, ',', '.') }}
                            </th>
                            <th class="border px-4 py-2 text-center font-bold text-red-700">
                                Rp {{ number_format(collect($keuanganPerUnit)->sum('total_pengeluaran'), 0, ',', '.') }}
                            </th>
                            <th class="border px-4 py-2 text-center font-bold text-blue-700">
                                Rp {{ number_format(collect($keuanganPerUnit)->sum('total_akhir'), 0, ',', '.') }}
                            </th>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>

        <!-- Grafik Transaksi Tabungan per Bulan -->
        <div class="p-6 bg-white rounded-lg shadow-md mb-6">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Grafik Tabungan Transaksi Setoran vs Penarikan per
                Bulan</h3>
            <canvas id="transaksiChart"></canvas>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <!-- Statistik Siswa -->
            <div class="p-6 bg-white border-l-4 border-green-500 rounded-lg shadow-md">
                <h3 class="text-xl font-semibold text-gray-800">Jumlah Siswa Aktif</h3>
                <p class="text-3xl font-bold text-green-700">{{ $siswaAktif }}</p>
            </div>

            <div class="p-6 bg-white border-l-4 border-red-500 rounded-lg shadow-md">
                <h3 class="text-xl font-semibold text-gray-800">Jumlah Siswa Non Aktif</h3>
                <p class="text-3xl font-bold text-red-700">{{ $siswaNonAktif }}</p>
            </div>

            <div class="p-6 bg-white border-l-4 border-purple-500 rounded-lg shadow-md">
                <h3 class="text-xl font-semibold text-gray-800">Total Siswa</h3>
                <p class="text-3xl font-bold text-purple-700">{{ $totalSiswa }}</p>
            </div>
        </div>
        <!-- Statistik Siswa per Unit Pendidikan -->
        <div class="overflow-x-auto bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Distribusi Siswa per Unit Pendidikan</h3>
            <table class="min-w-full bg-white border border-gray-200 text-sm rounded-lg shadow-sm">
                <thead class="bg-gray-100 text-gray-700">
                    <tr>
                        <th class="py-3 px-4 border-b">Unit Pendidikan</th>
                        <th class="py-3 px-4 border-b">Jumlah Siswa Aktif</th>
                        <th class="py-3 px-4 border-b">Jumlah Siswa Non Aktif</th>
                        <th class="py-3 px-4 border-b">Jumlah Siswa Pindah</th>
                        <th class="py-3 px-4 border-b">Jumlah Siswa Drop Out</th>
                        <th class="py-3 px-4 border-b">Jumlah Siswa Lulus</th>
                        <th class="py-3 px-4 border-b">Total Siswa</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($siswaPerUnit as $data)
                    <tr>
                        <td class="border px-4 py-2">
                            {{ $data->unitpendidikan->namaunit ?? '-' }}
                        </td>
                        <td class="border px-4 py-2 text-center">{{ $data->aktif }}</td>
                        <td class="border px-4 py-2 text-center">{{ $data->non_aktif }}</td>
                        <td class="border px-4 py-2 text-center">{{ $data->drop_out }}</td>
                        <td class="border px-4 py-2 text-center">{{ $data->lulus }}</td>
                        <td class="border px-4 py-2 text-center">{{ $data->pindah }}</td>
                        <td class="border px-4 py-2 text-center font-semibold">{{ $data->total }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>

    <script>
    console.log('Labels:', @json($labels));
    console.log('Setoran:', @json($setoranGabungan));
    console.log('Penarikan:', @json($penarikanDataFormatted));
    var ctx = document.getElementById('transaksiChart').getContext('2d');
    var chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($labels), // data bulan
            datasets: [{
                label: 'Setoran',
                data: @json($setoranGabungan), // Menggunakan setoranGabungan
                backgroundColor: 'rgba(40,167,69,0.6)',
                borderColor: 'rgba(40,167,69,1)',
                borderWidth: 1
            }, {
                label: 'Penarikan',
                data: @json($penarikanDataFormatted), // Menggunakan penarikanDataFormatted
                backgroundColor: 'rgba(255,99,132,0.6)',
                borderColor: 'rgba(255,99,132,1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Bulan'
                    }
                },
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Jumlah (Rp)'
                    }
                }
            }
        }
    });
    </script>

</x-layout-yayasan>
