<x-layout-tupusat>
    <x-slot name="header">
        {{-- Header, bisa ditambahkan sesuai kebutuhan --}}
    </x-slot>

    <div class="p-6 max-w-7xl mx-auto">
        <h4 class="text-xl font-semibold text-gray-700 mb-6">Daftar Siswa</h4>

        {{-- Filter Form --}}
        <form id="filterForm" method="GET" action="{{ route('tupusat.tagihan-siswa.index') }}" class="mb-6 flex flex-wrap gap-4 items-center">
            <div class="flex gap-3 items-center">
                <label for="unit" class="text-sm font-medium text-gray-700">Unit Pendidikan:</label>
                <select name="unit" id="unit" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 w-44">
                    <option value="">-- Semua Unit --</option>
                    @foreach($unitpendidikan as $u)
                        <option value="{{ $u->id }}" {{ $unit == $u->id ? 'selected' : '' }}>{{ $u->namaUnit }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-3 items-center">
                <label for="kelas" class="text-sm font-medium text-gray-700">Kelas:</label>
                <select name="kelas" id="kelas" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 w-44">
                    <option value="">-- Semua Kelas --</option>
                    @foreach($kelasList as $k)
                        <option value="{{ $k->id }}" {{ $kelas == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-3 items-center">
                <label for="search" class="text-sm font-medium text-gray-700">Cari Nama / NIS:</label>
                <input type="text" name="search" id="search" value="{{ $search }}" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 w-56" placeholder="Cari..." oninput="delaySubmit()">
            </div>
        </form>

        <form method="GET" action="{{ route('tupusat.tagihan.export-all') }}">
            <input type="hidden" name="unit" value="{{ request('unit') }}">
            <input type="hidden" name="kelas" value="{{ request('kelas') }}">
            <button type="submit"
                class="inline-block bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm font-medium transition">
                Download Excel
            </button>
        </form>

        {{-- Script untuk Delay Submit pada Search --}}
        <script>
            let typingTimer;
            function delaySubmit() {
                clearTimeout(typingTimer);
                typingTimer = setTimeout(() => {
                    document.getElementById('filterForm').submit();
                }, 500);
            }
        </script>

        <div class="overflow-x-auto rounded-lg shadow-lg border border-gray-200">
            <table class="min-w-full bg-white text-sm">
                <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3">Nama</th>
                        <th class="px-4 py-3">NIS</th>
                        <th class="px-4 py-3">Kelas</th>
                        <th class="px-4 py-3">Unit Pendidikan</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    @foreach ($siswas as $siswa)
                        <tr class="border-t hover:bg-gray-50">
                            <td class="px-4 py-3">{{ $siswa->nama }}</td>
                            <td class="px-4 py-3 text-center">{{ $siswa->nis }}</td>
                            <td class="px-4 py-3 text-center">{{ $siswa->kelas->nama_kelas ?? '-' }}</td>
                            <td class="px-4 py-3 text-center">{{ $siswa->unitPendidikan->namaUnit ?? '-' }}</td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('tupusat.tagihan.show', $siswa->id) }}"
                                   class="inline-block bg-teal-500 hover:bg-teal-600 text-white px-3 py-1 rounded text-xs font-medium transition">
                                    Rincian Tagihan
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $siswas->appends(request()->query())->links() }}
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function () {
            function loadKelas(unitId, selectedKelas = null) {
                $('#kelas').empty().append('<option value="">-- Semua Kelas --</option>');
                if (unitId) {
                    $.get("{{ route('tupusat.api.kelas') }}", { unit_id: unitId }, function (data) {
                        if (data.length === 0) return;
                        data.forEach(kelas => {
                            const isSelected = selectedKelas && selectedKelas.toString() === kelas.id.toString() ? 'selected' : '';
                            $('#kelas').append(`<option value="${kelas.id}" ${isSelected}>${kelas.nama_kelas}</option>`);
                        });
                    });
                }
            }

            $('#unit').change(function () {
                const unitId = $(this).val();
                loadKelas(unitId);
                $('#filterForm').submit();
            });

            $('#kelas').change(function () {
                $('#filterForm').submit();
            });

            @if(request('unit'))
                loadKelas('{{ request('unit') }}', '{{ request('kelas') ?? '' }}');
            @endif
        });
    </script>
</x-layout-tupusat>
