<x-layout-yayasan>
    <x-slot name="header"></x-slot>

    <div class="container mx-auto mt-8">
        <h1 class="text-2xl font-bold mb-4">Transaksi Kas yang Dihapus</h1>

        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4">
                {{ session('success') }}
            </div>
        @endif

        <table class="min-w-full table-auto bg-white shadow-md rounded-lg">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 text-left text-sm text-gray-700">Nama Kas</th>
                    <th class="px-4 py-2 text-left text-sm text-gray-700">Nominal</th>
                    <th class="px-4 py-2 text-left text-sm text-gray-700">Unit Pendidikan</th>
                    <th class="px-4 py-2 text-left text-sm text-gray-700">Deleted by</th>
                    <th class="px-4 py-2 text-left text-sm text-gray-700">Deleted At</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($trashedTransaksiKas as $transaksi)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2">{{ $transaksi->kas->namaKas ?? 'Kas tidak ditemukan' }}</td>
                    <td class="px-4 py-2">Rp. {{ number_format($transaksi->nominal, 2, ',', '.') }}</td>
                    <td class="px-4 py-2">{{ $transaksi->unitpendidikan->namaUnit }}</td>
                    <td class="px-4 py-2">{{ $transaksi->deleted_by }}</td>
                    <td class="px-4 py-2">{{ $transaksi->deleted_at->translatedFormat('d F Y h:i A') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-6">
            <a href="{{ route('yayasan.laporan.kas.index') }}" class="text-blue-600 hover:underline">← Kembali ke daftar transaksi</a>
        </div>
    </div>
</x-layout-yayasan>
