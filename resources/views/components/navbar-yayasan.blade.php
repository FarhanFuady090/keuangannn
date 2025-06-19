<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dropdownButtons = document.querySelectorAll('[data-collapse-toggle]');

        dropdownButtons.forEach(button => {
            button.addEventListener('click', function() {
                const dropdownId = this.getAttribute('aria-controls');
                const dropdownMenu = document.getElementById(dropdownId);

                // Toggle the visibility of the dropdown menu
                dropdownMenu.classList.toggle('hidden');
            });
        });
    });

</script>
<style>
    .navbar-sticky-side {
        position: fixed;
    }
</style>

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>


<aside id="sidebar" class="w-64 h-full navbar-sticky-side bg-green-800 transition-all duration-300 ease-in-out">
    <!-- Header -->
    <header class="bg-white shadow ">
        <div class="max-w-7xl mx-auto h-12 px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-12">
                <div class="flex-shrink-0">
                    <h1 class="text-xl font-bold text-gray-900">YAYASAN</h1>
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
                <x-nav-link href="{{ route('yayasan.dashboard') }}" :active="request()->routeIs('yayasan.dashboard')">
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
                <x-nav-link aria-controls="dropdown-laporan" data-collapse-toggle="dropdown-laporan"
                    :active="request()->routeIs('yayasan.laporan.siswa.index') ||
                            request()->routeIs('yayasan.laporan.siswa.show') ||
                            request()->routeIs('yayasan.laporan.kas.index') ||
                            request()->routeIs('yayasan.laporan.kas.show') ||
                            request()->routeIs('yayasan.laporan.kas.trashed') ||
                            request()->routeIs('yayasan.laporan.tabungan.index') ||
                            request()->routeIs('yayasan.laporan.tabungan.show')
                            ">
                            <!-- Ikon laporan -->
                            <svg class="flex-shrink-0 w-5 h-5 text-gray-100 transition duration-75 dark:text-gray-400 group-hover:text-gray-100 dark:group-hover:text-white"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 17v-2h6v2m-6-4v-2h6v2M5 12V5a2 2 0 012-2h6l5 5v10a2 2 0 01-2 2H7a2 2 0 01-2-2z" />
                            </svg>
                            <span class="text-white flex-1 ms-3 whitespace-nowrap">Laporan</span>
                            <svg class="dropdown-arrow w-4 h-4 transition-transform duration-100 transform"
                                xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                </x-nav-link>


                <ul id="dropdown-laporan"
                    class="py-2 space-y-2 {{
                    request()->routeIs('yayasan.laporan.siswa.index') ||
                    request()->routeIs('yayasan.laporan.siswa.show') ||
                    request()->routeIs('yayasan.laporan.kas.index') ||
                    request()->routeIs('yayasan.laporan.kas.trashed') ||
                    request()->routeIs('yayasan.laporan.tabungan.index') ||
                    request()->routeIs('yayasan.laporan.tabungan.show') ||
                    request()->routeIs('yayasan.laporan.tabungan.export')
                    ? '' : 'hidden' }}">

                    <!-- Submenu laporan -->
                    <li>
                        <a href="{{ route('yayasan.laporan.siswa.index') }}"
                            class="flex items-center w-full p-2 text-green-100 transition duration-75 rounded-lg pl-11 group hover:bg-green-800 dark:text-white dark:hover:bg-green-700
                            {{ request()->routeIs('yayasan.laporan.siswa.index') || request()->routeIs('yayasan.laporan.siswa.show') ? 'bg-green-700' : '' }}">
                            <!-- Ikon siswa -->
                            <svg class="flex-shrink-0 w-5 h-5 text-gray-100 transition duration-75 dark:text-gray-400 group-hover:text-gray-100 dark:group-hover:text-white"
                                xmlns="http://www.w3.org/1000/svg" viewBox="0 0 640 512" fill="currentColor" aria-hidden="true">
                                <path d="M622.3 271.1L416 176.5V133c0-7.1-3.8-13.6-10-17.2L336 66.5V32c0-8.8-7.2-16-16-16H48C39.2 16 32 23.2 32 32v400c0 8.8 7.2 16 16 16h272c8.8 0 16-7.2 16-16V233.1l70.2 33.1c-16.2 24.6-25.4 53.6-25.4 84.8 0 88.4 71.6 160 160 160s160-71.6 160-160c0-30.1-8.3-58.3-22.5-82.9z"/>
                            </svg>
                            <span class="text-white flex-1 ms-3 whitespace-nowrap">
                            Siswa</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('yayasan.laporan.kas.index') }}"
                            class="flex items-center w-full p-2 text-green-100 transition duration-75 rounded-lg pl-11 group hover:bg-green-800 dark:text-white dark:hover:bg-green-700
                            {{ request()->routeIs('yayasan.laporan.kas.index') || request()->routeIs('yayasan.laporan.kas.trashed') ? 'bg-green-700' : '' }}">
                            <!-- Ikon laporan -->
                        <svg class="flex-shrink-0 w-5 h-5 text-gray-100 transition duration-75 dark:text-gray-400 group-hover:text-gray-100 dark:group-hover:text-white"
                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" fill="currentColor" aria-hidden="true">
                            <path d="M64 96C28.7 96 0 124.7 0 160v192c0 35.3 28.7 64 64 64H512c35.3 0 64-28.7 64-64V160c0-35.3-28.7-64-64-64H64zM64 128H512c17.7 0 32 14.3 32 32v16c-17.7 0-32 14.3-32 32s14.3 32 32 32v16c0 17.7-14.3 32-32 32H64c-17.7 0-32-14.3-32-32V240c17.7 0 32-14.3 32-32s-14.3-32-32-32v-16c0-17.7 14.3-32 32-32zM288 208a48 48 0 1 0 0 96 48 48 0 1 0 0-96z"/>
                        </svg>

                            <span class="text-white flex-1 ms-3 whitespace-nowrap">
                            Kas</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('yayasan.laporan.tabungan.index') }}"
                            class="flex items-center w-full p-2 text-green-100 transition duration-75 rounded-lg pl-11 group hover:bg-green-800 dark:text-white dark:hover:bg-green-700
                            {{ request()->routeIs('yayasan.laporan.tabungan.index') || request()->routeIs('yayasan.laporan.tabungan.show') || request()->routeIs('yayasan.laporan.tabungan.export') ? 'bg-green-700' : '' }}">
                            <!-- Ikon laporan -->
                            <svg class="flex-shrink-0 w-5 h-5 text-gray-100 transition duration-75 dark:text-gray-400 group-hover:text-gray-100 dark:group-hover:text-white"
                                xmlns="http://www.w3.org/1000/svg" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M3 21v-2h18v2H3Zm16-4V9.83L12 4.12 5 9.83V17h2v-5h10v5h2ZM7 12v3h2v-3H7Zm4 0v3h2v-3h-2Zm4 0v3h2v-3h-2Z"/>
                            </svg>
                            <span class="text-white flex-1 ms-3 whitespace-nowrap">
                            Tabungan</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li>
                <form method="POST" action="{{ route('yayasan.logout') }}">
                    @csrf
                    <a href="{{ route('yayasan.logout') }}"
                        onclick="event.preventDefault(); this.closest('form').submit();">
                        <x-danger-button class="ms-9">
                            <span class ="text-white flex-1 ms-3 whitespace-nowrap">Logout</span>
                        </x-danger-button>
                    </a>
                </form>
            </li>
        </ul>

    </div>

    <div class="fixed bottom-0">
        <footer class="text-white text-center p-0 w-30">
            <p>copyright &copy; {{ date('Y') }} </p>
            <p>Yayasan Nurul Huda</p>
        </footer>
    </div>
</aside>
