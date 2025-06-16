<x-layout-tupusat>
    <x-slot name="header"></x-slot>

    <div class="max-w-2xl mx-auto px-6 py-8 bg-white shadow-md rounded-lg">
        <h4 class="text-xl font-semibold text-gray-800 mb-4">Bayar Tagihan:
            <span class="font-normal">{{ $tagihan->jenisPembayaran->nama_pembayaran ?? '-' }} - {{ $tagihan->siswa->nama ?? '-' }}</span>
        </h4>

        {{-- Back Button --}}
        <a href="{{ route('tupusat.tagihan.show', $tagihan->siswa_id) }}"
           class="inline-block mb-4 px-4 py-2 bg-gray-600 text-white text-sm rounded hover:bg-gray-700 transition">
            Kembali
        </a>

        {{-- Payment Form --}}
        <form action="{{ route('tupusat.tagihan.bayar.proses', $tagihan->id) }}" method="POST" class="space-y-6">
            @csrf

            {{-- Nominal Tagihan --}}
            <div class="space-y-1">
                <label class="block text-sm font-medium text-gray-700">Nominal Tagihan</label>
                <input type="text"
                       class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 text-gray-700 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-200"
                       value="{{ number_format($tagihan->nominal, 0, ',', '.') }}" disabled>
            </div>

            {{-- Jumlah Sudah Dibayar --}}
            <div class="space-y-1">
                <label class="block text-sm font-medium text-gray-700">Jumlah Sudah Dibayar</label>
                <input type="text"
                       class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 text-gray-700 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-200"
                       value="{{ number_format($tagihan->jumlah_dibayar, 0, ',', '.') }}" disabled>
            </div>

            {{-- Jumlah Bayar --}}
            <div class="space-y-1">
                <label for="jumlah_bayar" class="block text-sm font-medium text-gray-700">Jumlah Bayar (Rp)</label>
                <input type="number"
                       name="jumlah_bayar"
                       id="jumlah_bayar"
                       class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-2 focus:ring-indigo-200"
                       min="1"
                       max="{{ $tagihan->nominal - $tagihan->jumlah_dibayar }}"
                       required>
                @error('jumlah_bayar')
                    <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                @enderror
            </div>

            {{-- Tanggal Bayar --}}
            <div class="space-y-1">
                <label for="tanggal_bayar" class="block text-sm font-medium text-gray-700">Tanggal Bayar</label>
                <input type="date"
                       name="tanggal_bayar"
                       id="tanggal_bayar"
                       class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-2 focus:ring-indigo-200"
                       required
                       value="{{ date('Y-m-d') }}">
                @error('tanggal_bayar')
                    <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                @enderror
            </div>

            {{-- Submit Button --}}
            <div class="space-y-1">
                <button type="submit"
                        class="w-full px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition">
                    Bayar
                </button>
            </div>
        </form>
    </div>
</x-layout-tupusat>
