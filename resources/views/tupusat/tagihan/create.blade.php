<x-layout-tupusat>
    <x-slot name="header"></x-slot>

    <div class="max-w-6xl mx-auto px-6 py-8 bg-white rounded-xl shadow-md">
        <h4 class="text-2xl font-semibold text-gray-800 mb-6">Buat Tagihan Siswa</h4>

        @if(session('success'))
            <div class="mb-6 p-4 border border-green-300 bg-green-100 text-green-800 rounded-md">
                {{ session('success') }}
            </div>
        @endif

        {{-- Filter --}}
        <form id="filter-form" method="GET" action="{{ route('tupusat.tagihan.create') }}" class="mb-10">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="unit" class="block mb-1 text-sm font-medium text-gray-700">Unit Pendidikan</label>
                    <select name="unit" id="unit"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-500"
                        required>
                        <option value="">-- Pilih Unit --</option>
                        @foreach($unitpendidikan as $unit)
                            <option value="{{ $unit->id }}" {{ $selectedUnit == $unit->id ? 'selected' : '' }}>
                                {{ $unit->namaUnit }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="kelas" class="block mb-1 text-sm font-medium text-gray-700">Kelas</label>
                    <select name="kelas" id="kelas"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-500">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($kelasList as $kelas)
                            <option value="{{ $kelas->id }}" {{ ($selectedKelas == $kelas->id) ? 'selected' : '' }}>
                                {{ $kelas->nama_kelas }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="tahun" class="block mb-1 text-sm font-medium text-gray-700">Tahun Ajaran</label>
                    <select name="tahun" id="tahun"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-500"
                        required>
                        <option value="">-- Pilih Tahun --</option>
                        @foreach($tahunAjaran as $tahun)
                            <option value="{{ $tahun->id }}" {{ $selectedTahun == $tahun->id ? 'selected' : '' }}>
                                {{ $tahun->tahun_ajaran }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end">
                    <button type="submit"
                        class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition duration-200">
                        Tampilkan Manual
                    </button>
                </div>
            </div>
        </form>

        {{-- Form Tagihan --}}
        <form id="form-tagihan" action="{{ route('tupusat.tagihan.store') }}" method="POST">
            @csrf
            <input type="hidden" name="tahun_ajaran_id" id="tahun_ajaran_id" value="{{ $selectedTahun }}">

            <div class="mb-6">
                <label for="jenis_pembayaran_id" class="block mb-1 text-sm font-medium text-gray-700">Jenis Pembayaran</label>
                <select name="jenis_pembayaran_id" id="jenis_pembayaran_id"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-500"
                    required>
                    <option value="">-- Pilih Jenis --</option>
                    @foreach($jenisPembayaran as $jenis)
                        <option value="{{ $jenis->id }}">{{ $jenis->nama_pembayaran }} ({{ $jenis->type }}) - Rp {{ number_format($jenis->nominal_jenispembayaran, 0, ',', '.') }}</option>
                    @endforeach
                </select>
            </div>

            <div class="overflow-x-auto border border-gray-300 rounded-lg">
                <table class="min-w-full table-auto text-sm" id="siswa-table">
                    <thead class="bg-gray-100 text-left text-gray-700">
                        <tr>
                            <th class="px-4 py-3 font-semibold">
                                <input type="checkbox" id="select-all" class="form-checkbox">
                            </th>
                            <th class="px-4 py-3 font-semibold">Nama Siswa</th>
                            <th class="px-4 py-3 font-semibold">NIS</th>
                            <th class="px-4 py-3 font-semibold">Kelas</th>
                            <th class="px-4 py-3 font-semibold">Nominal Tagihan (Rp)</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($siswaList as $siswa)
                            <tr>
                                <td class="px-4 py-2">
                                    <input type="checkbox" name="siswa_ids[]" value="{{ $siswa->id }}" class="siswa-checkbox">
                                </td>
                                <td class="px-4 py-2">{{ $siswa->nama }}</td>
                                <td class="px-4 py-2">{{ $siswa->nis }}</td>
                                <td class="px-4 py-2">{{ $siswa->kelas->nama_kelas ?? '-' }}</td>
                                <td class="px-4 py-2">
                                <input type="hidden"
                                    name="tagihan[{{ $siswa->id }}]"
                                    class="nominal-input w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-300 focus:border-blue-500"
                                    value="{{ old('tagihan.' . $siswa->id, optional($jenisPembayaran->first())->nominal_jenispembayaran ?? '') }}"
                                    min="0" step="1000" placeholder="Nominal" readonly>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center px-4 py-3 text-gray-500">Belum ada data siswa</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(count($jenisPembayaran) && count($siswaList))
                <div class="mt-6 text-right">
                    <button type="submit"
                        class="inline-block px-6 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition duration-200">
                        Simpan Tagihan
                    </button>
                </div>
            @endif
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function () {
            function loadJenisPembayaran(unitId, tahunId) {
                $.get("{{ route('tupusat.api.jenispembayaran') }}", {
                    unit_id: unitId,
                    tahun_id: tahunId
                }, function (data) {
                    let $select = $('#jenis_pembayaran_id');
                    $select.empty().append('<option value="">-- Pilih Jenis --</option>');
                    data.forEach(jenis => {
                        $select.append(`<option value="${jenis.id}">${jenis.nama_pembayaran} (${jenis.type})</option>`);
                    });
                });
            }

            function loadSiswa(unitId, kelasId = null) {
                let params = { unit_id: unitId };
                if (kelasId) {
                    params.kelas_id = kelasId;
                }

                $.get("{{ route('tupusat.api.siswa') }}", params, function (data) {
                    let $tbody = $('#siswa-table tbody');
                    $tbody.empty();
                    if (data.length === 0) {
                        $tbody.append('<tr><td colspan="3" class="text-center px-4 py-3 text-gray-500">Tidak ada siswa tersedia.</td></tr>');
                        return;
                    }
                    data.forEach(siswa => {
                        $tbody.append(`
                            <tr>
                                <td class="px-4 py-2">${siswa.nama}</td>
                                <td class="px-4 py-2">${siswa.nis}</td>
                                <td class="px-4 py-2">
                                    <input type="number" name="tagihan[${siswa.id}]"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-300 focus:border-blue-500"
                                           min="0" step="1000" placeholder="Masukkan nominal">
                                </td>
                            </tr>
                        `);
                    });
                });

                function loadKelas(unitId, selectedKelas = null) {
                    $.get("{{ route('tupusat.api.kelas') }}", { unit_id: unitId }, function(data) {
                        let $kelasSelect = $('#kelas');
                        $kelasSelect.empty().append('<option value="">-- Pilih Kelas --</option>');
                        data.forEach(kelas => {
                            let selected = (kelas.id == selectedKelas) ? 'selected' : '';
                            $kelasSelect.append(`<option value="${kelas.id}" ${selected}>${kelas.nama_kelas}</option>`);
                        });
                    });
                }
            }

            $('#unit, #tahun').change(function () {
                let unitId = $('#unit').val();
                let tahunId = $('#tahun').val();
                $('#tahun_ajaran_id').val(tahunId);

                if (unitId && tahunId) {
                    loadJenisPembayaran(unitId, tahunId);
                    loadKelas(unitId);
                    loadSiswa(unitId);
                }
            });

            $(document).ready(function () {
                // Select All checkbox logic
                $('#select-all').on('change', function () {
                    var isChecked = $(this).prop('checked');
                    $('.siswa-checkbox').prop('checked', isChecked);  // Check/uncheck all siswa checkboxes
                });

                // Check if all siswa checkboxes are selected, and update the "Select All" checkbox accordingly
                $('.siswa-checkbox').on('change', function () {
                    var allChecked = $('.siswa-checkbox').length === $('.siswa-checkbox:checked').length;
                    $('#select-all').prop('checked', allChecked);
                });

                // Update nominal inputs when jenis pembayaran changes
                $('#jenis_pembayaran_id').on('change', function () {
                    let jenisId = $(this).val();
                    if (!jenisId) return;

                    $.get("{{ route('tupusat.api.jenispembayaran.nominal') }}", { id: jenisId }, function (data) {
                        if (data.nominal !== undefined) {
                            updateNominalInputs(data.nominal);
                            $('#preview-nominal').text("Nominal per siswa: Rp " + new Intl.NumberFormat('id-ID').format(data.nominal));
                        }
                    });

                function updateNominalInputs(nominal) {
                    $('.nominal-input').val(nominal);
                }
                });
            });
        });
    </script>
</x-layout-tupusat>
