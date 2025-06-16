<x-layout-tupusat>
    <x-slot name="header"></x-slot>

    <div class="max-w-3xl mx-auto mt-10 px-4">
        <div class="bg-white p-8 shadow-md rounded-lg">
            <h1 class="text-2xl font-semibold text-gray-800 mb-6">Edit Transaksi Kas</h1>

            <form action="{{ route('tupusat.kas.update', $transaksiKas->id) }}" method="POST" class="space-y-5">
                @csrf
                @method('PUT')

                <!-- Tanggal Bayar -->
                <div>
                    <label for="tanggal_bayar" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Bayar</label>
                    <input type="date" name="tanggal_bayar" id="tanggal_bayar"
                        value="{{ $transaksiKas->tanggal_bayar }}"
                        class="w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring focus:ring-blue-200"
                        required>
                </div>

                <!-- Kas -->
                <div>
                    <label for="kas_id" class="block text-sm font-medium text-gray-700 mb-1">Kas</label>
                    <select name="kas_id" id="kas_id" class="w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring focus:ring-blue-200" required>
                        @foreach ($kas as $item)
                            <option value="{{ $item->id }}" {{ $transaksiKas->kas_id == $item->id ? 'selected' : '' }}>
                                {{ $item->namaKas }} ({{ $item->kategori }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Nominal -->
                <div>
                    <label for="nominal" class="block text-sm font-medium text-gray-700 mb-1">Nominal</label>
                    <input type="number" name="nominal" id="nominal"
                        class="w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring focus:ring-blue-200"
                        value="{{ $transaksiKas->nominal }}" required>
                </div>

                <!-- Unit Pendidikan -->
                <div>
                    <label for="unitpendidikan_id" class="block text-sm font-medium text-gray-700 mb-1">Unit Pendidikan</label>
                    <select name="unitpendidikan_id" id="unitpendidikan_id"
                        class="w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring focus:ring-blue-200"
                        required>
                        @foreach ($unitPendidikan as $unit)
                            <option value="{{ $unit->id }}" {{ $transaksiKas->unitpendidikan_id == $unit->id ? 'selected' : '' }}>
                                {{ $unit->namaUnit }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Keterangan -->
                <div>
                    <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                    <textarea name="keterangan" id="keterangan" rows="3"
                        class="w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring focus:ring-blue-200">{{ $transaksiKas->keterangan }}</textarea>
                </div>

                <!-- Submit -->
                <div>
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-md transition duration-200">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layout-tupusat>
