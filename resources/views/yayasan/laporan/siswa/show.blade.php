<x-layout-yayasan>
    <x-slot name="header"></x-slot>

    <div class="max-w-4xl mx-auto mt-10">
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-2xl font-semibold mb-6">Detail Data Siswa</h2>

            <div class="space-y-4">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <!-- NIS -->
                    <div class="flex items-center">
                        <label class="w-1/3 text-sm font-medium text-gray-700">NIS:</label>
                        <input type="text" value="{{ $siswas->nis }}" class="w-2/3 p-2 border border-gray-300 rounded-md" readonly>
                    </div>

                    <!-- NISN -->
                    <div class="flex items-center">
                        <label class="w-1/3 text-sm font-medium text-gray-700">NISN:</label>
                        <input type="text" value="{{ $siswas->nisn }}" class="w-2/3 p-2 border border-gray-300 rounded-md" readonly>
                    </div>

                    <!-- Nama Siswa -->
                    <div class="flex items-center">
                        <label class="w-1/3 text-sm font-medium text-gray-700">Nama Siswa:</label>
                        <input type="text" value="{{ $siswas->nama }}" class="w-2/3 p-2 border border-gray-300 rounded-md" readonly>
                    </div>

                    <!-- Jenis Kelamin -->
                    <div class="flex items-center">
                        <label class="w-1/3 text-sm font-medium text-gray-700">Jenis Kelamin:</label>
                        <input type="text" value="{{ $siswas->jenis_kelamin }}" class="w-2/3 p-2 border border-gray-300 rounded-md" readonly>
                    </div>

                    <!-- Kelas -->
                    <div class="flex items-center">
                        <label class="w-1/3 text-sm font-medium text-gray-700">Kelas:</label>
                        <input type="text" value="{{ $siswas->kelas->nama_kelas }}" class="w-2/3 p-2 border border-gray-300 rounded-md" readonly>
                    </div>

                    <!-- Status -->
                    <div class="flex items-center">
                        <label class="w-1/3 text-sm font-medium text-gray-700">Status:</label>
                        <input type="text" value="{{ $siswas->status }}" class="w-2/3 p-2 border border-gray-300 rounded-md" readonly>
                    </div>

                    <!-- Agama -->
                    <div class="flex items-center">
                        <label class="w-1/3 text-sm font-medium text-gray-700">Agama:</label>
                        <input type="text" value="{{ $siswas->agama }}" class="w-2/3 p-2 border border-gray-300 rounded-md" readonly>
                    </div>

                    <!-- Nama Orang Tua / Wali -->
                    <div class="flex items-center">
                        <label class="w-1/3 text-sm font-medium text-gray-700">Nama Orang Tua / Wali:</label>
                        <input type="text" value="{{ $siswas->namaOrtu }}" class="w-2/3 p-2 border border-gray-300 rounded-md" readonly>
                    </div>

                    <!-- Alamat Orang Tua -->
                    <div class="flex items-center">
                        <label class="w-1/3 text-sm font-medium text-gray-700">Alamat Orang Tua:</label>
                        <input type="text" value="{{ $siswas->alamatOrtu }}" class="w-2/3 p-2 border border-gray-300 rounded-md" readonly>
                    </div>

                    <!-- No Telp. / WA Ortu -->
                    <div class="flex items-center">
                        <label class="w-1/3 text-sm font-medium text-gray-700">No Telp. / WA Ortu:</label>
                        <input type="text" value="{{ $siswas->noTelp }}" class="w-2/3 p-2 border border-gray-300 rounded-md" readonly>
                    </div>

                    <!-- Email -->
                    <div class="flex items-center">
                        <label class="w-1/3 text-sm font-medium text-gray-700">Email:</label>
                        <input type="email" value="{{ $siswas->email }}" class="w-2/3 p-2 border border-gray-300 rounded-md" readonly>
                    </div>

                    <!-- Unit Pendidikan Formal -->
                    <div class="flex items-center">
                        <label class="w-1/3 text-sm font-medium text-gray-700">Unit Pendidikan Formal:</label>
                        <input type="text" value="{{ optional($unitpendidikan->firstWhere('id', $siswas->unitpendidikan_id))->namaUnit }}" class="w-2/3 p-2 border border-gray-300 rounded-md" readonly>
                    </div>

                    <!-- Unit Pendidikan Informal -->
                    <div class="flex items-center">
                        <label class="w-1/3 text-sm font-medium text-gray-700">Unit Pendidikan Informal:</label>
                        <input type="text" value="{{ optional($unitpendidikan->firstWhere('id', $siswas->unitpendidikan_idInformal))->namaUnit }}" class="w-2/3 p-2 border border-gray-300 rounded-md" readonly>
                    </div>

                    <!-- Status Pondok -->
                    <div class="flex items-center">
                        <label class="w-1/3 text-sm font-medium text-gray-700">Status Pondok:</label>
                        <input type="text" value="{{ optional($unitpendidikan->firstWhere('id', $siswas->unitpendidikan_idPondok))->namaUnit }}" class="w-2/3 p-2 border border-gray-300 rounded-md" readonly>
                    </div>
                </div>

                <!-- Kembali Button -->
                <div class="flex justify-end mt-6">
                    <a href="{{ route('yayasan.laporan.siswa.index') }}">
                        <button class="bg-red-500 text-white px-6 py-2 rounded-md hover:bg-red-700" type="button">Kembali</button>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-layout-yayasan>
