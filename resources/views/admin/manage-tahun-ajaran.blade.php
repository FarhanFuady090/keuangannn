<x-layout-admin>
    <x-slot name="header">
    </x-slot>

    <html lang="en">
    <head>
        <meta charset="utf-8"/>
        <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
        <title>Manajemen Tahun Ajaran</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    </head>
    <body class="bg-gray-100">
    <div class="flex h-screen">
        <div class="flex-1 p-6">
            <div class="flex justify-between items-center mb-6">
                <div class="text-2xl font-bold">Manajemen Tahun Ajaran</div>
            </div>

            <div class="bg-white p-4 rounded shadow">
            @if (session('success'))
    <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">
        <strong>Sukses!</strong> {{ session('success') }}
    </div>
@endif


                <div class="flex justify-between items-center mb-4">
                    <div class="flex items-center space-x-4">
                        <!-- Filter Status -->
                        <form method="GET" action="{{ route('admin.manage-tahun-ajaran') }}" class="border border-gray-300 rounded p-2 text-sm">
    <select name="status" id="status" class="..." onchange="this.form.submit()">
        <option value="Semua" {{ request('status', 'Semua') == 'Semua' ? 'selected' : '' }}>Pilih Status</option>
        <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
        <option value="non aktif" {{ request('status') == 'non aktif' ? 'selected' : '' }}>Non Aktif</option>
    </select>

    <input type="hidden" name="entries" value="{{ request('entries', 10) }}">
    <input type="hidden" name="search" value="{{ request('search') }}">
</form>

                    </div>
                    <a href="{{ route('admin.create-tahun-ajaran') }}">
                        <x-primary-button class="bg-green-500 text-white px-4 py-2 rounded flex items-center">
                            <i class="fas fa-plus mr-2"></i>
                            {{ __('Tambah Data') }}
                        </x-primary-button>
                    </a>
                </div>

                <div class="flex justify-between items-center mb-4">
                    <!-- Filter Jumlah Entri -->
                    <form method="GET" action="{{ route('admin.manage-tahun-ajaran') }}" class="flex items-center space-x-2">
    <label class="text-sm" for="entries">Show</label>
    <select name="entries" id="entries" class="border border-gray-300 rounded p-2 text-sm" onchange="this.form.submit()">
        <option value="10" {{ request('entries', 10) == 10 ? 'selected' : '' }}>10</option>
        <option value="20" {{ request('entries', 10) == 20 ? 'selected' : '' }}>20</option>
        <option value="30" {{ request('entries', 10) == 30 ? 'selected' : '' }}>30</option>
        <option value="50" {{ request('entries', 10) == 50 ? 'selected' : '' }}>50</option>
    </select>
    <label class="text-sm" for="entries">entries</label>

    <input type="hidden" name="status" value="{{ request('status', 'Semua') }}">
    <input type="hidden" name="search" value="{{ request('search') }}">
</form>


                    <!-- Pencarian -->
                    <form id="searchForm" method="GET" action="{{ route('admin.manage-tahun-ajaran') }}" class="w-full sm:w-1/2 flex">
    <input
        class="block w-full p-3 border border-gray-300 rounded flex-grow"
        id="search"
        name="search"
        type="text"
        placeholder="Cari Tahun Ajaran..."
        value="{{ request('search') }}"
    />

    <input type="hidden" name="status" value="{{ request('status', 'Semua') }}">
    <input type="hidden" name="entries" value="{{ request('entries', 10) }}">
</form>

                </div>

                <div class="mb-4">
                    <span id="totalItems" class="text-sm font-medium text-gray-700">Total Data: {{ $tahunajaran->count() }}</span>
                </div>

                @if ($tahunajaran->isEmpty())
                    <div class="p-4 bg-yellow-200 text-yellow-800 rounded-lg mb-4">
                        <p>Maaf, saat ini tidak ada data Tahun Ajaran yang tersedia.</p>
                    </div>
                @endif

                <hr class="border-gray-300 mb-4" />

                <div class="overflow-x-auto">
                    <table id="data-table" class="min-w-full bg-white border border-gray-300">
                        <tr class="bg-green-500 text-white text-center">
                            <th class="py-2 px-4 border-r border-gray-300 w-16">No.</th>
                            <th class="py-2 px-4 border-r border-gray-300 w-60">Tahun Ajaran</th>
                            <th class="py-2 px-4 border-r border-gray-300">Awal</th>
                            <th class="py-2 px-4 border-r border-gray-300">Akhir</th>
                            <th class="py-2 px-4 border-r border-gray-300 w-60">Status</th>
                            <th class="py-2 px-4 border-r border-gray-300 w-24">Aksi</th>
                        </tr>
                        <tbody>
                        @foreach ($tahunajaran as $no=>$data)
                            <tr class="bg-white text-black text-center border-b border-gray-300">
                                <td class="py-2 px-4 border-r border-gray-300">{{ ($tahunajaran->currentPage() - 1) * $tahunajaran->perPage() + $loop->iteration }}</td>
                                <td class="py-2 px-4 border-r border-gray-300">{{ (int) $data->tahun_ajaran }}/{{ (int) $data->tahun_ajaran + 1 }}</td>
                                <td class="py-2 px-4 border-r border-gray-300">{{ $data->awal }}</td>
                                <td class="py-2 px-4 border-r border-gray-300">{{ $data->akhir }}</td>
                                <td class="py-2 px-4 border-r border-gray-300">
                                @if($data->status === 'Aktif')
          <span class="px-2 py-1 font-semibold text-green-700 bg-green-100 rounded-full">Aktif</span>
        @else
          <span class="px-2 py-1 font-semibold text-red-700 bg-red-100 rounded-full">Non Aktif</span>
        @endif
                                </td>
                                <td class="py-2 px-4 border-r border-gray-300">
                                    <a href="{{ route('admin.edit-tahun-ajaran', $data->id) }}"
                                       class="inline-flex items-center px-3 py-1.5 bg-yellow-500 text-white font-medium rounded hover:bg-yellow-900 transition duration-150 ease-in-out">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                             viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M15.232 5.232l3.536 3.536m-2.036-1.036L6.75 17.75a2 2 0 11-2.829-2.829L13.5 4.5m1.061-1.061L17.5 7.5m-3.061-4.061a2 2 0 113.536 1.536L8.75 16.25a2 2 0 11-2.829-2.829L13.5 4.5m1.061-1.061z"/>
                                        </svg>
                                        Edit
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $tahunajaran->appends([
                            'entries' => request('entries'),
                            'status' => request('status'),
                            'search' => request('search')
                        ])->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const searchInput = document.querySelector('input[name="search"]');
        const searchForm = document.getElementById('searchForm');

        let typingTimer;
        const typingDelay = 500;

        searchInput.addEventListener("input", function () {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(() => {
                searchForm.submit();
            }, typingDelay);
        });
    });
</script>

    </body>
    </html>
</x-layout-admin>
