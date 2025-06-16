<x-layout-yayasan>
    <x-slot name="header"></x-slot>

    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4">
            {{ session('error') }}
        </div>
    @endif

    <!-- Konten halaman -->
    <html lang="en">
        <head>
            <meta charset="utf-8" />
            <meta content="width=device-width, initial-scale=1.0" name="viewport" />
            <title>Laporan Data Siswa</title>
            <script src="https://cdn.tailwindcss.com"></script>
            <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
        </head>
        <body class="bg-gray-100">
            <div class="flex h-screen">
                <!-- Main Content -->
                <div class="flex-1 p-6">
                    <div class="flex justify-between items-center mb-6">
                        <div class="text-2xl font-bold">Laporan Data Siswa</div>
                    </div>
                    <div class="bg-white p-4 rounded shadow">
                    @if (session('success'))
    <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">
        <strong>Sukses!</strong> {{ session('success') }}
    </div>
@endif

                        <div class="flex justify-between items-center mb-4">
                            <div class="flex items-center space-x-4">
                        <form method="GET" action="{{ route('yayasan.laporan.siswa.index') }}" class="flex items-center space-x-4">
                            <!-- Filter Kelas -->
                            <select name="kelas_id" class="border border-gray-300 rounded p-2 text-sm">
                                <option value="">Pilih Kelas</option>
                                @foreach($kelas as $data)
                                    <option value="{{ $data->id }}" {{ request('kelas_id') == $data->id ? 'selected' : '' }}>{{ $data->nama_kelas }}</option>
                                @endforeach
                            </select>

                                       <!-- Filter unitpendidikan -->
                            <select name="unitpendidikan_id" class="border border-gray-300 rounded p-2 text-sm">
                                <option value="">Pilih Unit Pendidikan Formal</option>
                                @foreach($unitpendidikanformal as $data)
                                    <option value="{{ $data->id }}" {{ request('unitpendidikan_id') == $data->id ? 'selected' : '' }}>
                                        {{ $data->namaUnit }}
                                    </option>
                                @endforeach
                            </select>

                                    <select name="unitpendidikan_idInformal" class="border border-gray-300 rounded p-2 text-sm">
                                <option value="">Pilih Unit Pendidikan Informal</option>
                                @foreach($unitpendidikaninformal as $data)
                                    <option value="{{ $data->id }}" {{ request('unitpendidikan_idInformal') == $data->id ? 'selected' : '' }}>
                                        {{ $data->namaUnit }}
                                    </option>
                                @endforeach
                            </select>

                                    <select name="unitpendidikan_idPondok" class="border border-gray-300 rounded p-2 text-sm">
                                <option value="">Pilih Status Pondok</option>
                                @foreach($unitpendidikanpondok as $data)
                                    <option value="{{ $data->id }}" {{ request('unitpendidikan_idPondok') == $data->id ? 'selected' : '' }}>
                                        {{ $data->namaUnit }}
                                    </option>
                                @endforeach
                            </select>

                            <!-- Filter Status -->
                            <select name="status" class="border border-gray-300 rounded p-2 text-sm">
                                <option value="Pilih Status" {{ request('status') == 'Pilih Status' ? 'selected' : '' }}>Pilih Status</option>
                                <option value="Aktif" {{ request('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="Non Aktif" {{ request('status') == 'Non Aktif' ? 'selected' : '' }}>Non Aktif</option>
                                <option value="Drop Out" {{ request('status') == 'Drop Out' ? 'selected' : '' }}>Drop Out</option>
                                <option value="Pindah" {{ request('status') == 'Pindah' ? 'selected' : '' }}>Pindah</option>
                                <option value="Lulus" {{ request('status') == 'Lulus' ? 'selected' : '' }}>Lulus</option>
                            </select>

                            <!-- Tombol Tampilkan -->
                            <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded text-sm">Tampilkan</button>

                            <!-- Tombol Reset -->
                            <a href="{{ route('yayasan.laporan.siswa.index') }}"
                            class="bg-yellow-500 text-white px-4 py-2 rounded text-sm hover:bg-yellow-600 transition">
                                Reset
                            </a>
                        </form>

                            </div>
                        </div>
                        <div class="flex justify-between items-center mb-4">
                            <div class="flex items-center space-x-2">
                                <label class="text-sm" for="entries">Show</label>
                                <form method="GET" action="{{ route('yayasan.laporan.siswa.index') }}">
    <!-- Filter & search lainnya -->

    <select name="entries" class="border border-gray-300 rounded p-2 text-sm" onchange="this.form.submit()">
        <option value="10" {{ request('entries') == 10 ? 'selected' : '' }}>10</option>
        <option value="25" {{ request('entries') == 25 ? 'selected' : '' }}>25</option>
        <option value="50" {{ request('entries') == 50 ? 'selected' : '' }}>50</option>
        <option value="100" {{ request('entries') == 100 ? 'selected' : '' }}>100</option>
    </select>
</form>
                                <label class="text-sm" for="entries">entries</label>
                            </div>

                            <div class="w-full sm:w-1/2 flex">
                                <input
                                    class="block w-full p-3 border border-gray-300 rounded flex-grow"
                                    id="search"
                                    name="search"
                                    type="text"
                                    placeholder="Cari Nama atau NIS..."
                                    value="{{ request('search') }}"
                                />
                            </div>
                        </div>

                        <div class="mb-4">
                            <span id="totalItems" class="text-sm font-medium text-gray-700">Total Data: {{ $siswas->count() }}</span>
                        </div>

                        <div id="noDataMessage" class="p-4 bg-yellow-200 text-yellow-800 rounded-lg mb-4 hidden">
                            <p>Maaf, saat ini tidak ada data Siswa yang tersedia.</p>
                        </div>

                        <hr class="border-gray-300 mb-4" />

                        <!-- Table Data Siswa -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white border border-gray-300">
                        <thead>
                            <tr class="bg-green-500 text-white text-center">
                                <th class="py-2 px-4 border-r border-gray-300 w-16" rowspan="2">No.</th>
                                <th class="py-2 px-4 border-r border-gray-300" rowspan="2">NIS</th>
                                <th class="py-2 px-4 border-r border-gray-300" rowspan="2">Nama Siswa</th>
                                <th class="py-2 px-4 border-r border-gray-300" rowspan="2">Jenis Kelamin</th>
                                <th class="py-2 px-4 border-r border-gray-300" rowspan="2">Kelas</th>
                                <th class="py-2 px-4 border-r border-gray-300" colspan="2">Unit Pendidikan</th>
                                <th class="py-2 px-4 border-r border-gray-300" rowspan="2">Pondok</th>
                                <th class="py-2 px-4 border-r border-gray-300" rowspan="2">Status</th>
                                <th class="py-2 px-4 border-r border-gray-300" rowspan="2">Aksi</th>
                            </tr>
                            <tr class="bg-green-500 text-white text-center">
                                <th class="py-2 px-4 border-r border-gray-300">Formal</th>
                                <th class="py-2 px-4 border-r border-gray-300">Informal</th>
                            </tr>
                        </thead>
                         <tbody id="siswaTable">
    @foreach ($siswas as $no => $data)
        <tr class="bg-white text-black text-center border-b border-gray-300 siswa-row">
            <td class="py-2 px-4 border-r text-xs border-gray-300 w-16">{{ $no + 1 }}</td>
            <td class="py-2 px-4 border-r text-xs border-gray-300">{{ $data->nis ?? '-' }}</td>
            <td class="py-2 px-4 border-r text-xs border-gray-300">{{ $data->nama ?? '-' }}</td>
            <td class="py-2 px-4 border-r text-xs border-gray-300">{{ $data->jenis_kelamin ?? '-' }}</td>
            <td class="py-2 px-4 border-r text-xs border-gray-300">{{ $data->kelas->nama_kelas ?? '-' }}</td>
            <td class="py-2 px-4 border-r text-xs border-gray-300">{{ $data->unitpendidikan->namaUnit ?? '-' }}</td>
            <td class="py-2 px-4 border-r text-xs border-gray-300">
                {{ $data->unitpendidikan_idInformal ? optional($unitpendidikan->firstWhere('id', $data->unitpendidikan_idInformal))->namaUnit : '-' }}
            </td>
              <td class="py-2 px-4 border-r text-xs border-gray-300">
                {{ $data->unitpendidikan_idPondok ? optional($unitpendidikan->firstWhere('id', $data->unitpendidikan_idPondok))->namaUnit : '-' }}
            </td>
            <td class="py-2 px-4 border-r text-xs border-gray-300">
                @switch($data->status)
                    @case('Aktif')
                        <span class="px-2 py-1 font-semibold text-green-700 bg-green-100 rounded-full">Aktif</span>
                        @break
                    @case('Non Aktif')
                        <span class="px-2 py-1 font-semibold text-red-700 bg-red-100 rounded-full">Non Aktif</span>
                        @break
                    @case('Drop Out')
                        <span class="px-2 py-1 font-semibold text-yellow-800 bg-yellow-100 rounded-full">Drop Out</span>
                        @break
                    @case('Pindah')
                        <span class="px-2 py-1 font-semibold text-blue-700 bg-blue-100 rounded-full">Pindah</span>
                        @break
                    @case('Lulus')
                        <span class="px-2 py-1 font-semibold text-purple-700 bg-purple-100 rounded-full">Lulus</span>
                        @break
                    @default
                        <span class="px-2 py-1 font-semibold text-gray-700 bg-gray-100 rounded-full">Tidak Diketahui</span>
                @endswitch
            </td>
            <td class="py-2 px-4 border-r text-xs border-gray-300">
                <div class="flex justify-center space-x-2">
                    <a href="{{ route('yayasan.laporan.siswa.show', $data->id) }}"
                       class="inline-flex items-center px-3 py-1.5 bg-blue-500 text-white text-xs font-medium rounded hover:bg-blue-600 transition duration-150 ease-in-out">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        Detail
                    </a>
                </div>
            </td>
        </tr>
    @endforeach
</tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $siswas->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </body>
    </html>
</x-layout-yayasan>
