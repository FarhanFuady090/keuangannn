<x-layout-tupusat>
    <x-slot name="header"></x-slot>

    <div class="container mx-auto mt-10 px-4">
        <h1 class="text-2xl font-semibold text-gray-800 mb-6">Transaksi Kas yang Dihapus</h1>

        @if(session('success'))
            <div class="bg-green-100 border border-green-300 text-green-800 rounded-md px-4 py-3 mb-6 shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="min-w-full table-auto">
                <thead class="bg-gray-100 text-sm font-semibold text-gray-700">
                    <tr>
                        <th class="px-5 py-3 text-left">Nama Kas</th>
                        <th class="px-5 py-3 text-left">Nominal</th>
                        <th class="px-5 py-3 text-left">Unit Pendidikan</th>
                        <th class="px-5 py-3 text-left">Dihapus Oleh</th>
                        <th class="px-5 py-3 text-left">Waktu Dihapus</th>
                        <th class="px-5 py-3 text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-sm divide-y divide-gray-200">
                    @forelse ($trashedTransaksiKas as $transaksi)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-3">
                                {{ $transaksi->kas->namaKas ?? 'Kas tidak ditemukan' }}
                            </td>
                            <td class="px-5 py-3">
                                Rp. {{ number_format($transaksi->nominal, 2, ',', '.') }}
                            </td>
                            <td class="px-5 py-3">
                                {{ $transaksi->unitpendidikan->namaUnit }}
                            </td>
                            <td class="px-5 py-3">
                                {{ $transaksi->deleted_by }}
                            </td>
                            <td class="px-5 py-3">
                                {{ $transaksi->deleted_at->translatedFormat('d F Y H:i') }}
                            </td>
                            <td class="px-5 py-3 space-x-2">
                                <form action="{{ route('tupusat.kas.restore', $transaksi->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-blue-600 hover:text-blue-800 font-medium">Restore</button>
                                </form>

                                {{--
                                <form action="{{ route('tupusat.kas.forceDelete', $transaksi->id) }}" method="POST" onsubmit="return confirm('Hapus permanen?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 font-medium">Hapus Permanen</button>
                                </form>
                                --}}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-4 text-center text-gray-500">Tidak ada data transaksi yang dihapus.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            <a href="{{ route('tupusat.kas.index') }}" class="inline-block text-blue-600 hover:underline text-sm">
                ← Kembali ke daftar transaksi
            </a>
        </div>
    </div>
</x-layout-tupusat>
