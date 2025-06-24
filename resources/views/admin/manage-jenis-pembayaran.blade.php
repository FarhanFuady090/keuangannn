<x-layout-admin>
    <x-slot name="header"></x-slot>

    <html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta content="width=device-width, initial-scale=1.0" name="viewport" />
        <title>Manajemen Data Jenis Pemasukan</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    </head>
    <body class="bg-gray-100">
        <div class="flex h-screen">
            <div class="flex-1 p-6">
                <div class="flex justify-between items-center mb-6">
                    <div class="text-2xl font-bold">Manajemen Data Pemasukan</div>
                </div>

                <div class="bg-white p-4 rounded shadow">
                    <div class="flex justify-between items-center mb-4">
                        <form method="GET" action="{{ route('admin.manage-jenis-pembayaran') }}" class="flex items-center space-x-4">
                            <!-- Filter Unit -->
                            <select name="unitpendidikan" class="border border-gray-300 rounded p-2 text-sm" onchange="this.form.submit()">
                                <option value="">Pilih Unit</option>
                                @foreach ($unitpendidikan as $unit)
                                    <option value="{{ $unit->id }}" {{ (request('unitpendidikan') == $unit->id) ? 'selected' : '' }}>
                                        {{ $unit->namaUnit }}
                                    </option>
                                @endforeach
                            </select>

                            <!-- Filter Status -->
                            <select name="status" class="border border-gray-300 rounded p-2 text-sm" onchange="this.form.submit()">
                                <option value="">Pilih Status</option>
                                <option value="Aktif" {{ request('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="Non Aktif" {{ request('status') == 'Non Aktif' ? 'selected' : '' }}>Non Aktif</option>
                            </select>
                        </form>

                        <a href="{{ route('admin.create-jenis-pembayaran') }}">
                            <x-primary-button class="bg-green-500 text-white px-4 py-2 rounded flex items-center">
                                <i class="fas fa-plus mr-2"></i>
                                Tambah Data
                            </x-primary-button>
                        </a>
                    </div>

                    <!-- Show entries -->
                    <div class="flex justify-between items-center mb-4">
                        <div class="flex items-center space-x-2">
                            <form method="GET" action="{{ route('admin.manage-jenis-pembayaran') }}">
                                <label class="text-sm" for="show">Show</label>
                                <select class="border border-gray-300 rounded p-2" id="show" name="show" onchange="this.form.submit()">
                                    @foreach ([10, 25, 50, 1] as $option)
                                        <option value="{{ $option }}" {{ request('show') == $option ? 'selected' : '' }}>{{ $option }}</option>
                                    @endforeach
                                </select>
                                <label class="text-sm">entries</label>
                            </form>
                        </div>

                        <div class="w-full sm:w-1/2 flex">
                            <input class="block w-full p-3 border border-gray-300 rounded flex-grow" id="search" type="text" placeholder="Cari Nama Pembayaran..." />
                        </div>
                    </div>

                    <div class="mb-4">
                        <span id="totalItems" class="text-sm font-medium text-gray-700">Total Data: {{ $filtered_data->total() }}</span>
                    </div>

                    <div id="noDataMessage" class="p-4 bg-yellow-200 text-yellow-800 rounded-lg mb-4 hidden">
                        <p>Maaf, saat ini tidak ada data Kas yang tersedia.</p>
                    </div>

                    <hr class="border-gray-300 mb-4" />

                    <div class="overflow-x-auto">
                        <table id="data-table" class="min-w-full bg-white border border-gray-300">
                            <thead>
                                <tr class="bg-green-500 text-white text-center">
                                    <th class="py-2 px-4 border-r border-gray-300">No.</th>
                                    <th class="py-2 px-4 border-r border-gray-300">Unit</th>
                                    <th class="py-2 px-4 border-r border-gray-300">Nama Pembayaran</th>
                                    <th class="py-2 px-4 border-r border-gray-300">Tipe</th>
                                    <th class="py-2 px-4 border-r border-gray-300">Tahun</th>
                                    <th class="py-2 px-4 border-r border-gray-300">Nominal</th>
                                    <th class="py-2 px-4 border-r border-gray-300">Status</th>
                                    <th class="py-2 px-4 border-r border-gray-300">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="dataRows">
                                @foreach ($filtered_data as $item)
                                    <tr class="bg-white text-black text-center border-b border-gray-300" data-nama="{{ strtolower($item->nama_pembayaran) }} {{ strtolower($item->namaUnit) }}">
                                        <td class="py-2 px-4 border-r border-gray-300">{{ ($filtered_data->currentPage() - 1) * $filtered_data->perPage() + $loop->iteration }}</td>
                                        <td class="py-2 px-4 border-r border-gray-300">{{ $item->namaUnit }}</td>
                                        <td class="py-2 px-4 border-r border-gray-300">{{ $item->namaUnit }} - {{ $item->nama_pembayaran }}</td>
                                        <td class="py-2 px-4 border-r border-gray-300">{{ $item->type }}</td>
                                        <td class="py-2 px-4 border-r border-gray-300">{{ $item->tahun_ajaran }}/{{ $item->tahun_ajaran + 1 }}</td>
                                        <td class="py-2 px-4 border-r border-gray-300">{{ number_format($item->nominal_jenispembayaran, 0, ',', '.') }}</td>
                                        <td class="py-2 px-4 border-r border-gray-300">
                                            <span class="px-2 py-1 font-semibold rounded-full {{ $item->status == 'Aktif' ? 'text-green-700 bg-green-100' : 'text-red-700 bg-red-100' }}">
                                                {{ $item->status }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.edit-jenis-pembayaran', $item->id) }}"
                                               class="inline-flex items-center px-3 py-1.5 bg-yellow-500 text-white text-xs font-medium rounded hover:bg-yellow-900 transition">
                                                <i class="fas fa-edit mr-1"></i>Edit
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="mt-4">
                            {{ $filtered_data->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const searchInput = document.getElementById('search');
                const dataRows = document.querySelectorAll('#dataRows tr');
                const totalItemsElement = document.getElementById('totalItems');
                const noDataMessage = document.getElementById('noDataMessage');

                function updateTotalItems() {
                    const visibleRows = Array.from(dataRows).filter(row => row.style.display !== 'none');
                    totalItemsElement.textContent = `Total Data: ${visibleRows.length}`;
                    noDataMessage.classList.toggle('hidden', visibleRows.length > 0);
                }

                searchInput.addEventListener('input', () => {
                    const searchTerm = searchInput.value.toLowerCase();
                    dataRows.forEach(row => {
                        const content = row.getAttribute('data-nama');
                        row.style.display = content.includes(searchTerm) ? '' : 'none';
                    });
                    updateTotalItems();
                });

                updateTotalItems();
            });
        </script>
    </body>
    </html>
</x-layout-admin>
