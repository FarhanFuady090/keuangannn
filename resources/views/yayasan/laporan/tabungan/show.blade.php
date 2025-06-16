<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<x-layout-yayasan>
    <x-slot name="header"></x-slot>

    <div class="max-w-5xl mx-auto mt-10 px-6">
        <div class="bg-white shadow-lg rounded-lg p-6 mb-6">
            <h3 class="text-2xl font-semibold text-gray-800 mb-4">
                Detail Tabungan: <span class="text-green-600">{{ $tabungan->siswa->nama }}</span>
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div class="p-4 bg-green-50 border-l-4 border-green-400 rounded">
                    <p class="text-gray-600 text-sm">Setoran Awal</p>
                    <p class="text-lg font-semibold text-green-800">
                        Rp {{ number_format($tabungan->saldo_awal, 0, ',', '.') }}
                    </p>
                </div>
                <div class="p-4 bg-blue-50 border-l-4 border-blue-400 rounded">
                    <p class="text-gray-600 text-sm">Saldo Akhir</p>
                    <p class="text-lg font-semibold text-blue-800">
                        Rp {{ number_format($tabungan->saldo_akhir, 0, ',', '.') }}
                    </p>
                </div>
            </div>

            <div class="flex justify-end mb-4 space-x-2">
                <a href="{{ url( route('yayasan.laporan.tabungan.index')) }}" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">Kembali</a>
                <a href="{{ route('yayasan.laporan.tabungan.export.pdf', $tabungan->id) }}"
                   class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded transition duration-200">
                    Export PDF
                </a>
            </div>

            <!-- Grafik -->
            <div class="my-6">
                <h5 class="text-lg font-semibold text-gray-700 mb-3">Grafik Transaksi per Bulan</h5>
                <canvas id="transaksiChart" height="100"></canvas>
            </div>

            <!-- Filter Tanggal -->
            <form method="GET" class="flex items-center gap-2 mb-4">
                <label class="text-sm text-gray-700">Periode:</label>
                <input type="date" name="start" value="{{ request('start') }}"
                       class="border px-2 py-1 rounded">
                <span class="text-gray-600">s/d</span>
                <input type="date" name="end" value="{{ request('end') }}"
                       class="border px-2 py-1 rounded">
                <button type="submit"
                        class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded">
                    Terapkan
                </button>
                @if(request()->has('start') || request()->has('end'))
                  <a href="{{ route('yayasan.laporan.tabungan.show', $tabungan->id) }}"
                class="inline-block bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded">
                    Reset
                </a>
                @endif
            </form>

            <h5 class="text-lg font-semibold text-gray-700 mb-3">Riwayat Transaksi</h5>

            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-200 text-sm rounded-lg shadow-sm">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="py-3 px-4 border-b">Tanggal</th>
                            <th class="py-3 px-4 border-b">Jenis</th>
                            <th class="py-3 px-4 border-b">Jumlah</th>
                            <th class="py-3 px-4 border-b">Saldo</th>
                            <th class="py-3 px-4 border-b">Petugas</th>
                            <th class="py-3 px-4 border-b">Informasi</th>
                        </tr>
                    </thead>
                 <tbody>
                    {{-- Baris saldo awal --}}
                    <tr class="bg-gray-200 text-center">
                        <td class="py-2 px-4 border-b">{{ $tabungan->created_at->translatedFormat('d F Y h:i A') }}</td>
                        <td class="py-2 px-4 border-b font-semibold">Setoran Awal</td>
                        <td class="py-2 px-4 border-b text-green-700">
                            Rp {{ number_format($tabungan->saldo_awal, 0, ',', '.') }}
                        </td>
                          <td class="py-2 px-4 border-b text-blue-700">
                            Rp {{ number_format($tabungan->saldo_awal, 0, ',', '.') }}
                        </td>
                        <td class="py-2 px-4 border-b">{{ $tabungan->created_by }}</td>
                            @php
                                $info = json_decode($tabungan->information, true);
                            @endphp
                        <td class="py-2 px-4 border-b text-sm text-gray-700 leading-snug">
                                @if ($info)
                                    Telah dilakukan perubahan:
                                    @foreach ($info['perubahan'] ?? [] as $perubahan)
                                        <div>- {{ $perubahan }}</div>
                                    @endforeach
                                    <div class="mt-1 text-xs text-gray-500">
                                        Oleh {{ $info['oleh'] }} <br> pada {{ \Carbon\Carbon::parse($info['waktu'])->format('d/m/Y H:i A') }}
                                    </div>
                                @else
                                    <span class="text-gray-400 italic"></span>
                                @endif
                            </td>                        
                    </tr>

                    {{-- Riwayat transaksi --}}
                    @forelse ($transaksi as $trx)
                        <tr class="hover:bg-gray-50 text-center">
                            <td class="py-2 px-4 border-b">{{ $trx->created_at->translatedFormat('d F Y h:i A') }}</td>
                            <td class="py-2 px-4 border-b">{{ $trx->jenis_transaksi }}</td>
                            <td class="py-2 px-4 border-b {{ $trx->jenis_transaksi == 'Penarikan' ? 'text-red-600' : 'text-green-700' }}">
                                Rp {{ number_format($trx->jumlah, 0, ',', '.') }}
                            </td>
                               <td class="py-2 px-4 border-b text-blue-700">
                                Rp {{ number_format($trx->saldo_berjalan, 0, ',', '.') }}
                            </td>
                            <td class="py-2 px-4 border-b">{{ $trx->petugas }}</td>
                            @php
                                $info = json_decode($trx->information, true);
                            @endphp

                            <td class="py-2 px-4 border-b text-sm text-gray-700 leading-snug">
                                @if ($info)
                                    Telah dilakukan perubahan:
                                    @foreach ($info['perubahan'] ?? [] as $perubahan)
                                        <div>- {{ $perubahan }}</div>
                                    @endforeach
                                    <div class="mt-1 text-xs text-gray-500">
                                        Oleh {{ $info['oleh'] }} <br> pada {{ \Carbon\Carbon::parse($info['waktu'])->format('d/m/Y H:i A') }}
                                    </div>
                                @else
                                    <span class="text-gray-400 italic"></span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-4 text-center text-gray-500">Belum ada transaksi.</td>
                        </tr>
                    @endforelse
                </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- Grafik Chart.js -->
    <script>
        const ctx = document.getElementById('transaksiChart').getContext('2d');
        const transaksiChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($chartData->pluck('bulan')) !!},
                datasets: [
                    {
                        label: 'Setoran',
                        backgroundColor: 'rgba(34,197,94,0.7)',
                        data: {!! json_encode($chartData->pluck('total_setoran')) !!}
                    },
                    {
                        label: 'Penarikan',
                        backgroundColor: 'rgba(239,68,68,0.7)',
                        data: {!! json_encode($chartData->pluck('total_penarikan')) !!}
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });
    </script>
</x-layout-yayasan>