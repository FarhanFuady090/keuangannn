<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<x-layout-yayasan>
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
        <h1 class="text-3xl font-bold text-center text-gray-800 mb-8">Laporan Kas dan Transaksi Kas</h1>

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
            <div class="flex flex-wrap justify-center gap-4 mb-8">
            <a href="{{ route('yayasan.laporan.kas.trashed') }}"
                class="inline-block bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-medium px-6 py-2 rounded-lg transition duration-200 shadow">
                Transaksi Dihapus
            </a>
        </div>
    <form method="GET" action="{{ route('yayasan.laporan.kas.index') }}" class="flex flex-wrap gap-3 items-center mb-4">

    <!-- Filter Kas -->
    <select name="kas" class="border border-gray-300 rounded p-2 text-sm">
        <option value="">Pilih Kas</option>
        @foreach($filterKas as $kasItem)
            <option value="{{ $kasItem->id }}" {{ request('kas') == $kasItem->id ? 'selected' : '' }}>
                {{ $kasItem->namaKas }}
            </option>
        @endforeach
    </select>

    <!-- Filter Unit Pendidikan -->
    <select name="unit_pendidikan" class="border border-gray-300 rounded p-2 text-sm">
        <option value="">Pilih Unit Pendidikan</option>
        @foreach($unitPendidikanFilter as $unit)
            <option value="{{ $unit->id }}" {{ request('unit_pendidikan') == $unit->id ? 'selected' : '' }}>
                {{ $unit->namaunit }}
            </option>
        @endforeach
    </select>

    <!-- Filter Created By -->
    <select name="created_by" class="border border-gray-300 rounded p-2 text-sm">
        <option value="">Pilih Pembuat</option>
        @foreach($createdByUsers as $user)
            <option value="{{ $user }}" {{ request('created_by') == $user ? 'selected' : '' }}>{{ $user }}</option>
        @endforeach
    </select>
<!-- Filter Tanggal -->
<input type="date" name="tanggal_awal" class="border border-gray-300 rounded p-2 text-sm"
       value="{{ request('tanggal_awal') }}">
<span class="mx-1">s/d</span>
<input type="date" name="tanggal_akhir" class="border border-gray-300 rounded p-2 text-sm"
       value="{{ request('tanggal_akhir') }}">

       <!-- Filter Bulan -->
<select name="bulan" class="border border-gray-300 rounded p-2 text-sm">
    <option value="">Pilih Bulan</option>
    @for ($i = 1; $i <= 12; $i++)
        <option value="{{ $i }}" {{ request('bulan') == $i ? 'selected' : '' }}>
            {{ DateTime::createFromFormat('!m', $i)->format('F') }}
        </option>
    @endfor
</select>

<!-- Filter Tahun -->
<select name="tahun" class="border border-gray-300 rounded p-2 text-sm">
    <option value="">Pilih Tahun</option>
    @for ($year = now()->year; $year >= 2020; $year--)
        <option value="{{ $year }}" {{ request('tahun') == $year ? 'selected' : '' }}>
            {{ $year }}
        </option>
    @endfor
</select>


    <!-- Tombol Tampilkan dan Reset -->
    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded text-sm">Tampilkan</button>
    <a href="{{ route('yayasan.laporan.kas.index') }}" class="bg-gray-300 text-gray-800 px-4 py-2 rounded text-sm hover:bg-gray-400">Reset</a>
</form>



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
                            <th class="px-4 py-3">Nama Kas</th>
                            <th class="px-4 py-3">Tipe</th>
                            <th class="px-4 py-3">Nominal</th>
                            <th class="px-4 py-3">Unit Pendidikan</th>
                            <th class="px-4 py-3">Created By</th>
                            <th class="px-4 py-3">Created At</th>
                            <th class="px-4 py-3">Keterangan</th>
                            <th class="px-4 py-3">Informasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transaksiKas as $key => $transaksi)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-4 py-3">{{ $key + 1 }}</td>
                                <td class="px-4 py-3">{{ $transaksi->kas ? $transaksi->kas->namaKas : 'Kas tidak ditemukan' }}</td>
                                <td class="px-4 py-3">{{ $transaksi->tipe }}</td>
                                <td class="px-4 py-3">Rp. {{ number_format($transaksi->nominal, 2, ',', '.') }}</td>
                                <td class="px-4 py-3">{{ $transaksi->unitpendidikan->namaUnit }}</td>
                                <td class="px-4 py-3">{{ $transaksi->created_by }}</td>
                                <td class="px-4 py-3">{{ $transaksi->created_at->format('d/m/Y H:i A') }}</td>
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
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layout-yayasan>
