<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<x-layout-tupusat>
    <x-slot name="header"></x-slot>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded relative mb-4 shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded relative mb-4 shadow-sm">
            {{ session('error') }}
        </div>
    @endif

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
        <h1 class="text-3xl font-bold text-center text-gray-800 mb-8">Daftar Kas dan Transaksi Kas</h1>

        <!-- Daftar Kas -->
        <div class="bg-white rounded-lg shadow-md mb-8 p-6">
            <div class="flex justify-between items-center border-b pb-4 mb-4">
                <h2 class="text-xl font-semibold text-gray-700">Daftar Kas</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm text-left text-gray-700">
                    <thead class="bg-gray-100 text-xs uppercase text-gray-600">
                        <tr>
                            <th class="px-4 py-3">No</th>
                            <th class="px-4 py-3">Nama Kas</th>
                            <th class="px-4 py-3">Kategori</th>
                            <th class="px-4 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($kas as $key => $item)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-4 py-3">{{ $key + 1 }}</td>
                                <td class="px-4 py-3">{{ $item->namaKas }}</td>
                                <td class="px-4 py-3">{{ $item->kategori }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-block px-2 py-1 text-xs font-medium rounded-full
                                        {{ $item->status == 'Aktif' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        {{ $item->status }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tombol Aksi -->
        <div class="flex flex-wrap justify-center gap-4 mb-8">
            <a href="{{ route('tupusat.kas.create') }}"
                class="inline-block bg-green-600 hover:bg-green-700 text-white text-sm font-medium px-6 py-2 rounded-lg transition duration-200 shadow">
                Tambah Transaksi Kas
            </a>
            <a href="{{ route('tupusat.kas.trashed') }}"
                class="inline-block bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-medium px-6 py-2 rounded-lg transition duration-200 shadow">
                Transaksi Dihapus
            </a>
        </div>

        <!-- Daftar Transaksi Kas -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-center border-b pb-4 mb-4">
                <h2 class="text-xl font-semibold text-gray-700">Daftar Transaksi Kas</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm text-left text-gray-700">
                    <thead class="bg-gray-100 text-xs uppercase text-gray-600">
                        <tr>
                            <th class="px-4 py-3">No</th>
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3">Nama Kas</th>
                            <th class="px-4 py-3">Tipe</th>
                            <th class="px-4 py-3">Nominal</th>
                            <th class="px-4 py-3">Unit</th>
                            <th class="px-4 py-3">Created By</th>
                            <th class="px-4 py-3">Keterangan</th>
                            <th class="px-4 py-3">Informasi</th>
                            <th class="px-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transaksiKas as $key => $transaksi)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-4 py-3">{{ $key + 1 }}</td>
                                <td class="px-4 py-3">{{ \Carbon\Carbon::parse($transaksi->tanggal_bayar)->format('d/m/Y') }}</td>
                                <td class="px-4 py-3">{{ $transaksi->kas ? $transaksi->kas->namaKas : 'Kas tidak ditemukan' }}</td>
                                <td class="px-4 py-3">{{ $transaksi->tipe }}</td>
                                <td class="px-4 py-3">Rp. {{ number_format($transaksi->nominal, 2, ',', '.') }}</td>
                                <td class="px-4 py-3">{{ $transaksi->unitpendidikan->namaUnit }}</td>
                                <td class="px-4 py-3">{{ $transaksi->created_by }}</td>
                                <td class="px-4 py-3">{{ $transaksi->keterangan }}</td>
                                <td class="px-4 py-3">
                                    @php $info = json_decode($transaksi->information, true); @endphp
                                    @if ($info)
                                        <div class="text-sm">
                                            Telah dilakukan perubahan:
                                            @foreach ($info['perubahan'] ?? [] as $perubahan)
                                                <div class="ml-2 text-gray-600">- {{ $perubahan }}</div>
                                            @endforeach
                                            <div class="mt-1 text-xs text-gray-500">
                                                Oleh {{ $info['oleh'] }} <br>
                                                pada {{ \Carbon\Carbon::parse($info['waktu'])->format('d/m/Y H:i A') }}
                                            </div>
                                        </div>
                                    @else
                                        <span class="italic text-gray-400">Tidak ada info</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-center">
                                        <a href="{{ route('tupusat.kas.edit', $transaksi->id) }}"
                                            class="text-yellow-500 hover:text-yellow-700">Edit</a> <br>
                                        <form action="{{ route('tupusat.kas.destroy', $transaksi->id) }}"
                                            method="POST" class="inline-block"
                                            onsubmit="return confirm('Yakin hapus?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layout-tupusat>
