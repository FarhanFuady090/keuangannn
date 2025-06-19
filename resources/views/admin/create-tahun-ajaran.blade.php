<x-layout-admin>
    <x-slot name="header"></x-slot>

    <div class="max-w-4xl mx-auto mt-10">
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-2xl font-semibold mb-6">Tambah Tahun Ajaran</h2>

            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if ($errors->any())
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: '{{ $errors->first() }}',
        });
    </script>
@endif


            <form action="{{ route('admin.submitTahunAjaran') }}" method="POST">
                @csrf
                <div class="space-y-6">
                    <!-- Tahun Ajaran -->
                    <div class="flex items-center">
                        <label class="w-1/3 text-sm font-medium text-gray-700">Tahun Ajaran:</label>
                        <input class="w-2/3 p-2 border border-gray-300 rounded-md" type="text" name="tahun_ajaran" required />
                    </div>

                    <!-- Awal -->
                    <div class="flex items-center">
                        <label class="w-1/3 text-sm font-medium text-gray-700">Awal:</label>
                        <input class="w-2/3 p-2 border border-gray-300 rounded-md" type="date" name="awal" required />
                    </div>

                    <!-- Akhir -->
                    <div class="flex items-center">
                        <label class="w-1/3 text-sm font-medium text-gray-700">Akhir:</label>
                        <input class="w-2/3 p-2 border border-gray-300 rounded-md" type="date" name="akhir" required />
                    </div>

                    <!-- Status -->
                    <div class="flex items-center">
                        <label class="w-1/3 text-sm font-medium text-gray-700">Status:</label>
                        <select class="w-2/3 p-2 border border-gray-300 rounded-md bg-gray-100" name="status_display" disabled>
                            <option value="Aktif" selected>Aktif</option>
                        </select>
                        <!-- Input hidden untuk mengirimkan nilai status ke server -->
                        <input type="hidden" name="status" value="Aktif">
                    </div>
                </div>

                <!-- Button Kembali dan Simpan -->
                <div class="flex justify-end mt-6 space-x-4">
                    <a href="{{ route('admin.manage-tahun-ajaran') }}">
                        <button class="bg-blue-500 text-white px-6 py-2 rounded-md hover:bg-blue-700" type="button">Kembali</button>
                    </a>
                    <button type="reset" class="bg-orange-500 text-white px-4 py-2 rounded-md hover:bg-orange-600">Reset</button>
                    <button class="bg-green-500 text-white px-6 py-2 rounded-md hover:bg-green-700" type="submit">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function updateEndDate() {
            let awalInput = document.querySelector("input[name='awal']");
            let akhirInput = document.querySelector("input[name='akhir']");

            awalInput.addEventListener("change", function () {
                if (awalInput.value) {
                    let awalDate = new Date(awalInput.value);
                    awalDate.setMonth(awalDate.getMonth() + 6); // Tambah 6 bulan

                    // Format YYYY-MM-DD untuk input date
                    let akhirDate = awalDate.toISOString().split("T")[0];

                    // Set nilai pada input akhir
                    akhirInput.value = akhirDate;
                }
            });
        }

        window.onload = updateEndDate;
    </script>
</x-layout-admin>
