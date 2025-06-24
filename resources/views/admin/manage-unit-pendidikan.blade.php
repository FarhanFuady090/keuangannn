<x-layout-admin>
    <x-slot name="header">

    </x-slot>
    
    <html lang="en">
 <head>
  <meta charset="utf-8"/>
  <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
  <title>
   Manajemen Data Unit Pendidikan
  </title>
  <script src="https://cdn.tailwindcss.com">
  </script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
 </head>
 <body class="bg-gray-100">
  <div class="flex h-screen">
   <!-- Main Content -->
   <div class="flex-1 p-6">
    <div class="flex justify-between items-center mb-6">
     <div class="text-2xl font-bold">
      Manajemen Data Unit Pendidikan
     </div>
    </div>
    <div class="bg-white p-4 rounded shadow">
    @if (session('success'))
    <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">
        <strong>Sukses!</strong> {{ session('success') }}
    </div>
@endif
     <div class="flex justify-between items-center mb-4">
      <div class="flex items-center space-x-4">
      <form method="GET" action="{{ route('admin.manage-unit-pendidikan') }}" class="flex items-center space-x-4">
    <!-- Filter Kategori -->
    <select name="kategori" class="border border-gray-300 rounded p-2 text-sm">
        <option value="">Pilih Kategori</option>
        @foreach ($kategoriList as $kategori)
            <option value="{{ $kategori }}" {{ request('kategori') == $kategori ? 'selected' : '' }}>
                {{ $kategori }}
            </option>
        @endforeach
    </select>

    <!-- Filter Nama Unit Pendidikan -->
    <select name="namaUnit" class="border border-gray-300 rounded p-2 text-sm">
        <option value="">Pilih Nama Unit Pendidikan</option>
        @foreach ($namaUnitList as $unit)
            <option value="{{ $unit }}" {{ request('namaUnit') == $unit ? 'selected' : '' }}>
                {{ $unit }}
            </option>
        @endforeach
    </select>

    <select name="status" class="border border-gray-300 rounded p-2 text-sm">
        <option value="">Pilih Status</option>
        <option value="Aktif" {{ request('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
        <option value="Tidak Aktif" {{ request('status') == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
    </select>

    <!-- Submit Button -->
    <button type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded text-sm">Tampilkan</button>
</form>
      </div>
      <div class="flex items-center space-x-4">
       <a href="{{ route('admin.create-manage-unit-pendidikan') }}">
                    <x-primary-button class="bg-green-500 text-white px-4 py-2 rounded flex items-center">
                    <i class="fas fa-plus mr-2">
                    </i>
                        {{ __('Tambah Data') }}
                    </x-primary-button>
        </a>
      </div>
     </div>
     <div class="flex justify-between items-center mb-4">
      <div class="flex items-center space-x-2">
      <form method="GET" action="{{ route('admin.manage-unit-pendidikan') }}" class="flex items-center space-x-2">
    <label class="text-sm" for="entries">Show</label>
    <select class="border border-gray-300 rounded p-2 text-sm" id="entries" name="entries" onchange="this.form.submit()">
        @foreach([10, 20, 30, 40, 100] as $num)
            <option value="{{ $num }}" {{ request('entries') == $num ? 'selected' : '' }}>{{ $num }}</option>
        @endforeach
    </select>
    <label class="text-sm" for="entries">entries</label>

    <!-- Hidden filters to preserve selected values -->
    <input type="hidden" name="kategori" value="{{ request('kategori') }}">
    <input type="hidden" name="namaUnit" value="{{ request('namaUnit') }}">
    <input type="hidden" name="status" value="{{ request('status') }}">
</form>

      </div>
     </div>
     <div class="mb-4">
     <span id="totalItems" class="text-sm font-medium text-gray-700">Total Data: {{ $unitpendidikan->total() }}</span>

                            </div>

                            @if ($unitpendidikan->isEmpty())
                            <div class="p-4 bg-yellow-200 text-yellow-800 rounded-lg mb-4">
                                <p>Maaf, saat ini tidak ada data Unit Pendidikan yang tersedia.</p>
                            </div>
                            @endif

                            <hr class="border-gray-300 mb-4" />
                            
     <div class="overflow-x-auto">
     <table class="min-w-full bg-white border border-gray-300">
        <tr class="bg-green-500 text-white text-center">
         <th class="py-2 px-4 border-r border-gray-300 w-16">No.</th>
         <th class="py-2 px-4 border-r border-gray-300">Kategori</th>
         <th class="py-2 px-4 border-r border-gray-300">Nama Unit </th> 
         <th class="py-2 px-4 border-r border-gray-300">Status</th>
         <th class="py-2 px-4 border-r border-gray-300 w-24">Aksi</th>
        </tr>
        @foreach ($unitpendidikan as $no=>$data)
        <tr class="bg-white text-black text-center border-b border-gray-300">
        <td class="py-2 px-4 border-r border-gray-300 w-16">{{ ($unitpendidikan->currentPage() - 1) * $unitpendidikan->perPage() + $no + 1 }}</td>
         <td class="py-2 px-4 border-r border-gray-300">{{ $data->kategori }}</td>
         <td class="py-2 px-4 border-r border-gray-300">{{ $data->namaUnit }}</td>
         <td class="py-2 px-4 border-r border-gray-300">
         @if($data->status === 'Aktif')
          <span class="px-2 py-1 font-semibold text-green-700 bg-green-100 rounded-full">Aktif</span>
        @else
          <span class="px-2 py-1 font-semibold text-red-700 bg-red-100 rounded-full">Non Aktif</span>
        @endif
         </td>
         <td class="flex justify-center space-x-2 py-2 w-24">
            <a href="{{ route('admin.edit-manage-unit-pendidikan', $data->id) }}"
            class="inline-flex items-center px-3 py-1.5 bg-yellow-500 text-white font-medium rounded hover:bg-yellow-900 transition duration-150 ease-in-out">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-1.036L6.75 17.75a2 2 0 11-2.829-2.829L13.5 4.5m1.061-1.061L17.5 7.5m-3.061-4.061a2 2 0 113.536 1.536L8.75 16.25a2 2 0 11-2.829-2.829L13.5 4.5m1.061-1.061z" />
                                                    </svg>
                                                    Edit
                                                </a>
            </form>
         </td>
        </tr>
        @endforeach
    </table>
    <div class="mt-4">
    {{ $unitpendidikan->appends([
        'kategori' => request('kategori'),
        'namaUnit' => request('namaUnit'),
        'status' => request('status'),
        'entries' => request('entries'),
    ])->links() }}
</div>
     </div>
    </div>
   </div>
  </div>
 </body>
</html>
</x-layout-admin>