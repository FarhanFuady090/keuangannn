<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Sidebar toggle
        const sidebar = document.getElementById('sidebar');
        const toggleButton = document.getElementById('toggleSidebar');
        if (toggleButton && sidebar) {
            toggleButton.addEventListener('click', function () {
                sidebar.classList.toggle('-translate-x-full');
            });
        }

        // Dropdown toggle (collapsible menu)
        const dropdownButtons = document.querySelectorAll('[data-collapse-toggle]');
        dropdownButtons.forEach(button => {
            button.addEventListener('click', function (e) {
                e.preventDefault();
                const dropdownId = this.getAttribute('aria-controls');
                const dropdownMenu = document.getElementById(dropdownId);

                if (dropdownMenu) {
                    dropdownMenu.classList.toggle('hidden');

                    // Optional: Rotate arrow icon
                    const arrow = this.querySelector('svg#dropdown-arrow');
                    if (arrow) {
                        arrow.classList.toggle('rotate-180');
                    }
                }
            });
        });
    });
</script>


<style>
    .navbar-sticky-side {
        position: fixed;
    }
    .rotate-180 {
    transform: rotate(180deg);
}

</style>

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>


<aside id="sidebar" class="w-64 h-full navbar-sticky-side bg-green-800 transition-all duration-300 ease-in-out">
    <!-- Header -->
    <header class="bg-white shadow ">
        <div class="max-w-7xl mx-auto h-12 px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-12">
                <div class="flex-shrink-0">
                    <h1 class="text-xl font-bold text-gray-900">TU PUSAT</h1>
                </div>
            </div>
        </div>
        <button id="toggleSidebar" class="text-black p-2 focus:outline-none md:hidden">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
    </header>

    <div class="h-full px-3 py-4 overflow-y-auto bg-primary">
        {{-- user profile --}}
        <div class="flex items-center">
            <img alt="User profile picture" class="rounded-full" height="50" src="{{ asset('/images/profile.png') }}"
                width="50">
            <ul class="space-y-2 font-medium">
                <li>
                    <div class="ml-4 hidden-on-minimized flex items-center">
                    <span class="text-gray-100 dark:text-white">{{ Auth::guard()->user()->username }}</span>
                    </div>
                </li>
                <li>
                    <div class="ml-4 hidden-on-minimized flex items-center">
                        <span class="text-gray-100 dark:text-white">Role: {{ Auth::guard()->name }}</span>
                    </div>
                </li>
            </ul>
        </div>

        <div class="h-5 py-4 bg-primary">
            <hr class="10px">
        </div>

        <ul class="space-y-2 font-medium">
        <li>
    <x-nav-link href="{{ route('tupusat.dashboard.index') }}" :active="request()->routeIs('tupusat.dashboard.index')">
        <svg class="w-5 h-5 text-gray-100 transition duration-75 dark:text-gray-400 group-hover:text-gray-400 dark:group-hover:text-white"
            aria-hidden="true" xmlns="http://www.w3.org/1000/svg" fill="currentColor" viewBox="0 0 22 21">
            <path
                d="M16.975 11H10V4.025a1 1 0 0 0-1.066-.998 8.5 8.5 0 1 0 9.039 9.039.999.999 0 0 0-1-1.066h.002Z" />
            <path
                d="M12.5 0c-.157 0-.311.01-.565.027A1 1 0 0 0 11 1.02V10h8.975a1 1 0 0 0 1-.935c.013-.188.028-.374.028-.565A8.51 8.51 0 0 0 12.5 0Z" />
        </svg>
        <span class="text-white text-white flex-1 ms-3 whitespace-nowrap">Dashboard</span>
    </x-nav-link>
</li>

<li>
                <x-nav-link href="{{ route('tupusat.tagihan.create') }}" :active="request()->routeIs('tupusat.tagihan.create')">
                    <svg class="flex-shrink-0 w-5 h-5 text-gray-100 transition duration-75 dark:text-gray-400 group-hover:text-gray-100 dark:group-hover:text-white"
                        aria-hidden="true" xmlns="http://www.w3.org/1000/svg" fill="currentColor" viewBox="0 0 20 18">
                        <path
                            d="M14 2a3.963 3.963 0 0 0-1.4.267 6.439 6.439 0 0 1-1.331 6.638A4 4 0 1 0 14 2Zm1 9h-1.264A6.957 6.957 0 0 1 15 15v2a2.97 2.97 0 0 1-.184 1H19a1 1 0 0 0 1-1v-1a5.006 5.006 0 0 0-5-5ZM6.5 9a4.5 4.5 0 1 0 0-9 4.5 4.5 0 0 0 0 9ZM8 10H5a5.006 5.006 0 0 0-5 5v2a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-2a5.006 5.006 0 0 0-5-5Z" />
                    </svg>
                    <span class="text-white text-white flex-1 ms-3 whitespace-nowrap">Buat Tagihan</span>
                    <span
                        class="inline-flex items-center justify-center px-2 ms-3 text-sm font-medium text-gray-100 bg-gray-100 rounded-full dark:bg-gray-700 dark:text-gray-300"></span>
                </x-nav-link>
            </li>

<li>
    <x-nav-link aria-controls="dropdown-kelas" data-collapse-toggle="dropdown-kelas"
        :active="request()->routeIs('tupusat.tagihan-siswa.index') || request()->routeIs('tupusat.tagihan.show') ||
                 request()->routeIs('tupusat.kas.index') ||  request()->routeIs('tupusat.kas.create') ||  request()->routeIs('tupusat.kas.trashed') ||  request()->routeIs('tupusat.kas.edit')"
        @click="toggleDropdown()">
        <!-- Ikon utama menu Manage Kelas -->
        <svg class="flex-shrink-0 w-5 h-5 text-gray-100 transition duration-75 dark:text-gray-400 group-hover:text-gray-100 dark:group-hover:text-white"
            xmlns="http://www.w3.org/1000/svg" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path d="M4 4h16v2H4V4zm0 4h10v2H4V8zm0 4h16v2H4v-2zm0 4h10v2H4v-2z"/>
        </svg>
        <span class="text-white flex-1 ms-3 whitespace-nowrap">Transaksi</span>
        <svg id="dropdown-arrow" class="w-4 h-4 transition-transform duration-100 transform"
            :class="{ 'rotate-180': dropdownOpen }" xmlns="http://www.w3.org/1000/svg" fill="none"
            viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </x-nav-link>

    <ul id="dropdown-kelas"
    class="py-2 space-y-2 {{ request()->routeIs('tupusat.tagihan-siswa.index') ||  request()->routeIs('tupusat.tagihan.show') || request()->routeIs('tupusat.kas.index')  || request()->routeIs('tupusat.kas.trashed')  ||  request()->routeIs('tupusat.kas.create') ||  request()->routeIs('tupusat.kas.edit') ? '' : 'hidden' }}">

        <!-- Submenu CRU Kelas -->
        <li>
        <a href="{{ route('tupusat.tagihan-siswa.index') }}"
        class="flex items-center w-full p-2 text-green-100 transition duration-75 rounded-lg pl-11 group hover:bg-green-800 dark:text-white dark:hover:bg-green-700
                {{ request()->routeIs('tupusat.tagihan-siswa.index') || request()->routeIs('tupusat.tagihan.show') ? 'bg-green-700' : '' }}">
                <!-- Ikon CRU -->
                <svg class="w-4 h-4 me-2 text-white dark:text-white" xmlns="http://www.w3.org/1000/svg"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4v16m8-8H4" />
                </svg>
                <span class="text-white flex-1 ms-3 whitespace-nowrap">
                Tagihan</span>
            </a>
        </li>

        <!-- Submenu Perpindahan Kelas -->
        <li>
        <a href="{{ route('tupusat.kas.index') }}"
        class="flex items-center w-full p-2 text-green-100 transition duration-75 rounded-lg pl-11 group hover:bg-green-500 dark:text-white dark:hover:bg-green-700
                {{ request()->routeIs(('tupusat.kas.index')) || request()->routeIs(('tupusat.kas.create')) || request()->routeIs(('tupusat.kas.trashed')) || request()->routeIs(('tupusat.kas.edit')) ? 'bg-green-700' : '' }}">
                <!-- Ikon Perpindahan -->
                <svg class="w-4 h-4 me-2 text-white dark:text-white" xmlns="http://www.w3.org/1000/svg"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
                <span class="flex-1 ms-3 whitespace-nowrap">
                Kas</span>
            </a>
        </li>
    </ul>
</li>

            <li>
    <x-nav-link href="{{ route('tupusat.tabungan.index') }}" :active="request()->routeIs('tupusat.tabungan.index') ||request()->routeIs('tupusat.tabungan.edit') ||  request()->routeIs('tupusat.transaksi.edit') || request()->routeIs('tupusat.transaksi.create') || request()->routeIs('tupusat.tabungan.create') || request()->routeIs('tupusat.tabungan.create') || request()->routeIs('tabungan.show')">
        <!-- Ikon gedung/sekolah -->
        <svg class="flex-shrink-0 w-5 h-5 text-gray-100 transition duration-75 dark:text-gray-400 group-hover:text-gray-100 dark:group-hover:text-white"
            xmlns="http://www.w3.org/1000/svg" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path d="M3 21v-2h18v2H3Zm16-4V9.83L12 4.12 5 9.83V17h2v-5h10v5h2ZM7 12v3h2v-3H7Zm4 0v3h2v-3h-2Zm4 0v3h2v-3h-2Z"/>
        </svg>
        <span class="text-white flex-1 ms-3 whitespace-nowrap">Tabungan</span>
        <span
            class="inline-flex items-center justify-center px-2 ms-3 text-sm font-medium text-gray-100 bg-gray-100 rounded-full dark:bg-gray-700 dark:text-gray-300"></span>
    </x-nav-link>
</li>
            <li>
                <form method="POST" action="{{ route('tupusat.logout') }}">
                    @csrf
                    <a href="{{ route('tupusat.logout') }}"
                        onclick="event.preventDefault(); this.closest('form').submit();">
                        <x-danger-button class="ms-9">
                            <span class ="text-white flex-1 ms-3 whitespace-nowrap">Logout</span>
                        </x-danger-button>
                    </a>
                </form>
            </li>
        </ul>

    </div> <div class="fixed bottom-0">
        <footer class="text-white text-center p-0 w-30">
            <p>copyright &copy; {{ date('Y') }} </p>
            <p>Yayasan Nurul Huda</p>
        </footer>
    </div>
</aside>
