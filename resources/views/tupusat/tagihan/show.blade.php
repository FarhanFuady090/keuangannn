<x-layout-tupusat>
    <x-slot name="header"></x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Header Title -->
        <h4 class="text-2xl font-semibold text-gray-800 mb-6">Rincian Tagihan: {{ $siswa->nama }}</h4>

        <!-- Success Message -->
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        <!-- Action Buttons -->
        <div class="flex space-x-4 mb-6">
            <a href="{{ route('tupusat.tagihan-siswa.index') }}" class="inline-block px-4 py-2 bg-gray-600 text-white text-sm rounded hover:bg-gray-700 transition">
                Kembali ke Daftar Siswa
            </a>
            <a href="{{ route('tupusat.tagihan.cetak', $siswa->id) }}" class="inline-block px-4 py-2 bg-indigo-600 text-white text-sm rounded hover:bg-indigo-700 transition">
                Cetak Histori Tagihan
            </a>
            <!-- Button Download Excel -->
            <a href="{{ route('tupusat.tagihan.export-excel', $siswa->id) }}" class="inline-block px-4 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 transition">
                Download Excel
            </a>
        </div>

        <!-- Filter Form -->
        <form method="GET" action="{{ route('tupusat.tagihan.show', $siswa->id) }}" class="mb-6 flex items-center space-x-4">
            <label for="jenis_pembayaran" class="text-sm font-semibold text-gray-700">Filter Jenis Pembayaran:</label>
            <select name="jenis_pembayaran" id="jenis_pembayaran" onchange="this.form.submit()" class="text-sm border-gray-300 rounded p-2">
                <option value="">Semua</option>
                @foreach($jenisPembayaranList as $jenis)
                    <option value="{{ $jenis->id }}" {{ request('jenis_pembayaran') == $jenis->id ? 'selected' : '' }}>
                        {{ $jenis->nama_pembayaran }} ({{ $jenis->type }})
                    </option>
                @endforeach
            </select>

            <!-- Filter Status -->
            <label for="status" class="text-sm font-semibold text-gray-700">Filter Status:</label>
            <select name="status" id="status" onchange="this.form.submit()" class="text-sm border-gray-300 rounded p-2">
                <option value="">Semua</option>
                <option value="lunas" {{ request('status') == 'lunas' ? 'selected' : '' }}>Lunas</option>
                <option value="belum" {{ request('status') == 'belum' ? 'selected' : '' }}>Belum Lunas</option>
            </select>

            <!-- Show Entries Dropdown -->
            <label for="perPage" class="text-sm font-semibold text-gray-700">Show Entries:</label>
            <select name="perPage" id="perPage" onchange="this.form.submit()" class="text-sm border-gray-300 rounded p-2">
                <option value="15" {{ request('perPage') == '15' ? 'selected' : '' }}>15</option>
                <option value="30" {{ request('perPage') == '30' ? 'selected' : '' }}>30</option>
                <option value="50" {{ request('perPage') == '50' ? 'selected' : '' }}>50</option>
                <option value="100" {{ request('perPage') == '100' ? 'selected' : '' }}>100</option>
            </select>
        </form>



        <!-- Table -->
        <div class="overflow-x-auto bg-white shadow rounded-lg">
            <form method="POST" action="{{ route('tupusat.tagihan.bulkKwitansi') }}" target="_blank">
                @csrf
                <div class="mb-4 mt-4 mr-4 flex justify-end">
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 text-sm">
                        Cetak Kwitansi Terpilih
                    </button>
                </div>

                <!-- Table Content -->
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-center">
                                <input type="checkbox" id="selectAll" class="form-checkbox text-indigo-600">
                            </th>
                            <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Jenis Pembayaran</th>
                            <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 text-center">Tahun Ajaran</th>
                            <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 text-center">Bulan</th>
                            <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 text-center">Nominal</th>
                            <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 text-center">Jumlah Dibayar</th>
                            <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 text-center">Status</th>
                            <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 text-center">Tanggal Bayar</th>
                            <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($tagihans as $tagihan)
                            <tr>
                                <td class="px-4 py-2 text-center">
                                    @if($tagihan->status === 'lunas')
                                        <input type="checkbox" name="tagihan_ids[]" value="{{ $tagihan->id }}" class="tagihan-checkbox">
                                    @endif
                                </td>
                                <td class="px-4 py-2 text-sm text-gray-800">
                                    {{ $tagihan->jenisPembayaran->nama_pembayaran ?? '-' }} ({{ $tagihan->jenisPembayaran->type ?? '-' }})
                                </td>
                                <td class="px-4 py-2 text-sm text-gray-800 text-center">{{ $tagihan->tahunAjaran->tahun_ajaran ?? '-' }}</td>
                                <td class="px-4 py-2 text-sm text-gray-800 text-center">{{ $tagihan->bulan ?? '-' }}</td>
                                <td class="px-4 py-2 text-sm text-gray-800 text-center">{{ number_format($tagihan->nominal, 0, ',', '.') }}</td>
                                <td class="px-4 py-2 text-sm text-gray-800 text-center">{{ number_format($tagihan->jumlah_dibayar, 0, ',', '.') }}</td>
                                <td class="px-4 py-2 text-sm text-center">
                                    @if($tagihan->status == 'lunas')
                                        <span class="inline-block px-2 py-1 text-xs font-semibold bg-green-200 text-green-800 rounded">Lunas</span>
                                    @else
                                        <span class="inline-block px-2 py-1 text-xs font-semibold bg-yellow-200 text-yellow-800 rounded">Belum Lunas</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2 text-sm text-gray-800 text-center">{{ $tagihan->tanggal_bayar ?? '-' }}</td>
                                <td class="px-4 py-2 text-sm space-x-2 text-center">
                                    @if($tagihan->status != 'lunas')
                                        <a href="{{ route('tupusat.tagihan.bayar.form', $tagihan->id) }}" class="px-3 py-1 bg-blue-600 text-white rounded text-xs hover:bg-blue-700 transition">Bayar</a>
                                    @else
                                        <a href="{{ route('tupusat.tagihan.cetak.kwitansi', $tagihan->id) }}" target="_blank" class="inline-flex items-center justify-center w-6 h-6 bg-green-600 text-white rounded hover:bg-green-700 transition" title="Cetak Kwitansi">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2m-4 0h-4m4 0v4H8v-4m4 0h0"/>
                                            </svg>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-4 text-center text-sm text-gray-500">
                                    Belum ada tagihan untuk siswa ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </form>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $tagihans->links() }}
        </div>
    </div>

    <script>
        document.getElementById('selectAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.tagihan-checkbox');
            checkboxes.forEach(cb => cb.checked = this.checked);
        });
    </script>
</x-layout-tupusat>
