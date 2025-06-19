<x-layout-admin>
    <x-slot name="header">
        <!-- Tambahkan konten header jika diperlukan -->
    </x-slot>

    <style>
        .w-fixed-small {
            width: 60px; /* Role */
        }

        .w-fixed-medium {
            width: 100px; /* Unit */
        }
    </style>

    <div class="max-w-4xl mx-auto mt-10">
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-2xl font-bold mb-6">Tambah Akun User</h2>

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

            <form id="userForm" method="POST" action="{{ route('admin.submitUser') }}" class="space-y-4">
                @csrf

                <div id="feedback" class="hidden text-center"></div>
                <!-- Input Nama User -->
                <div class="flex items-center space-x-4">
                    <label for="nama_user" class="text-sm font-medium text-gray-700 w-1/4">Nama User</label>
                    <input type="text" id="nama_user" name="nama_user" class="w-3/4 px-4 py-2 border rounded-md focus:ring focus:ring-green-300" required>
                </div>

                <!-- Input Email -->
                <div class="flex items-center space-x-4">
                    <label for="email" class="text-sm font-medium text-gray-700 w-1/4">Email</label>
                    <input type="email" id="email" name="email" class="w-3/4 px-4 py-2 border rounded-md focus:ring focus:ring-green-300" required>
                </div>

                <!-- Input Nomor Telepon -->
                <div class="flex items-center space-x-4">
                    <label for="no_telp" class="text-sm font-medium text-gray-700 w-1/4">No. Telp/WA</label>
                    <input type="text" id="no_telp" name="no_telp" pattern="\d+" inputmode="numeric" minlength="10" class="w-3/4 px-4 py-2 border rounded-md focus:ring focus:ring-green-300" required>
                </div>

                <!-- Input Username -->
                <div class="flex items-center space-x-4">
                    <label for="username" class="text-sm font-medium text-gray-700 w-1/4">Username</label>
                    <input type="text" id="username" name="username" class="w-3/4 px-4 py-2 border rounded-md focus:ring focus:ring-green-300" required>
                </div>

                <!-- Input Password -->
                <div class="flex items-center space-x-4">
                    <label for="password" class="text-sm font-medium text-gray-700 w-1/4">Password</label>
                    <input type="password" id="password" name="password" class="w-3/4 px-4 py-2 border rounded-md focus:ring focus:ring-green-300" required>
                </div>

                <!-- Dropdown Peran User -->
                <div class="flex items-center space-x-4">
                    <label for="peran_user" class="text-sm font-medium text-gray-700 w-1/4">Peran User</label>
                    <select id="peran_user" name="peran_user" required class="w-3/4 px-4 py-2 border rounded-md focus:ring focus:ring-green-300" required>
                        <option value="">Pilih Peran</option>
                        <option value="admin">Admin</option>
                        <option value="yayasan">Yayasan</option>
                        <option value="tupusat">TU Pusat</option>
                        <option value="tuunit">TU Unit</option>
                    </select>
                </div>

                <!-- Dropdown Unit Pendidikan -->
                <div id="unit_pendidikan_div" class="flex items-center space-x-4 hidden">
                    <label for="namaUnit" class="text-sm font-medium text-gray-700 w-1/4">Unit Pendidikan</label>
                    <select id="namaUnit" name="namaUnit" class="w-3/4 px-4 py-2 border rounded-md focus:ring focus:ring-green-300">
                        <option value="">Pilih Pendidikan</option>
                        <option value="TK">TK</option>
                        <option value="SD">SD</option>
                        <option value="SMP">SMP</option>
                        <option value="SMA">SMA</option>
                        <option value="TPQ">TPQ</option>
                        <option value="YA PONDOK">PONDOK</option>
                        <option value="MADIN">MADIN</option>
                    </select>
                </div>

                <!-- Tindakan tombol -->
                <div class="flex justify-end space-x-4">
                    <a href="{{ route('admin.manage-user') }}" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 inline-flex items-center">
                        Kembali
                    </a>
                    <button type="reset" class="bg-orange-500 text-white px-4 py-2 rounded-md hover:bg-orange-600">Reset</button>
                    <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600">Simpan</button>
                </div>


            </form>
        </div>
    </div>

    <!-- JavaScript untuk Peran dan Unit Pendidikan -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const peranUser = document.getElementById('peran_user');
            const unitPendidikanDiv = document.getElementById('unit_pendidikan_div');
            const unitPendidikanSelect = document.getElementById('namaUnit');

            const toggleUnitPendidikan = () => {
                if (peranUser.value === 'tuunit') {
                    unitPendidikanDiv.classList.remove('hidden');
                } else {
                    unitPendidikanDiv.classList.add('hidden');
                    unitPendidikanSelect.value = ''; // Clear value if hidden
                }
            };

            peranUser.addEventListener('change', toggleUnitPendidikan);
            toggleUnitPendidikan(); // Call on load
        });
    </script>
</x-layout-admin>
