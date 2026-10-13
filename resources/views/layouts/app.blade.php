<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - SIKAD MAN 20 Jakarta</title>
    
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- Font Awesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Smooth Sidebar Collapse classes */
        #sidebar {
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1), transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        @media (min-width: 1024px) {
            #sidebar.sidebar-collapsed-desktop {
                margin-left: -18rem; /* -w-72 (288px) */
            }
        }

        /* Table responsiveness helpers */
        .table-responsive-container {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
    </style>
    @stack('styles')
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen flex flex-col">

    <div class="flex h-screen overflow-hidden relative">

        <!-- MOBILE BACKDROP OVERLAY -->
        <div id="sidebarBackdrop" 
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-40 hidden transition-opacity duration-300 lg:hidden"
             onclick="toggleSidebar(false)">
        </div>

        <!-- SIDEBAR -->
        <aside id="sidebar" 
               class="fixed inset-y-0 left-0 z-50 w-72 bg-slate-900 text-white flex flex-col flex-shrink-0 shadow-2xl -translate-x-full lg:translate-x-0 lg:static lg:z-30 lg:shadow-xl">
            
            <!-- Brand / Logo Header -->
            <div class="p-4 sm:p-5 border-b border-slate-800 flex items-center justify-between">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 overflow-hidden">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-emerald-500 to-blue-600 flex items-center justify-center text-white shadow-lg shadow-blue-500/30 flex-shrink-0">
                        <i class="fa-solid fa-calendar-check text-lg"></i>
                    </div>
                    <div class="truncate">
                        <h1 class="font-extrabold text-base tracking-tight leading-tight text-white">SIKAD MAN 20</h1>
                        <p class="text-[11px] text-slate-400 font-medium">Jadwal Pelajaran & Guru</p>
                    </div>
                </a>

                <!-- Close / Hide Buttons -->
                <div class="flex items-center gap-1">
                    <!-- Desktop Collapse Button -->
                    <button id="desktopCollapseBtn" 
                            onclick="toggleSidebar(false)"
                            title="Sembunyikan Sidebar (Desktop)"
                            class="hidden lg:flex w-8 h-8 rounded-lg items-center justify-center text-slate-400 hover:text-white hover:bg-slate-800 transition">
                        <i class="fa-solid fa-angles-left text-sm"></i>
                    </button>

                    <!-- Mobile Close Button -->
                    <button id="mobileCloseBtn" 
                            onclick="toggleSidebar(false)"
                            title="Tutup Menu"
                            class="lg:hidden w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-white hover:bg-slate-800 transition">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 overflow-y-auto px-3 sm:px-4 py-4 space-y-1.5">
                <div class="px-3 pb-2 text-[11px] font-semibold tracking-wider text-slate-400 uppercase">Menu Utama</div>

                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('dashboard') || request()->routeIs('home') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <i class="fa-solid fa-gauge-high w-5 text-center text-base"></i>
                    <span>Dashboard</span>
                </a>

                <!-- Penjadwalan -->
                <a href="{{ route('schedules.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('schedules.index') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <i class="fa-solid fa-table-cells w-5 text-center text-base"></i>
                    <span>Jadwal Pelajaran</span>
                </a>

                <!-- Deteksi Bentrok Jadwal -->
                @php $conflictCount = \App\Models\Schedule::count() > 0 ? (new \App\Services\ScheduleGeneratorService())->detectConflicts() : []; @endphp
                <a href="{{ route('schedules.conflicts') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('schedules.conflicts') ? 'bg-amber-500 text-slate-950 font-bold shadow-md shadow-amber-500/30' : 'text-amber-400 hover:bg-slate-800 hover:text-amber-300' }}">
                    <i class="fa-solid fa-triangle-exclamation w-5 text-center text-sm"></i>
                    <span>Deteksi Bentrok</span>
                    @if(count($conflictCount) > 0)
                        <span class="ml-auto bg-amber-500/20 text-amber-300 border border-amber-500/30 text-[10px] font-bold px-2 py-0.5 rounded-full">{{ count($conflictCount) }}</span>
                    @else
                        <span class="ml-auto bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-[10px] font-bold px-2 py-0.5 rounded-full">0</span>
                    @endif
                </a>

                <!-- Jadwal Guru Piket -->
                <a href="{{ route('pikets.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('pikets.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30 font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <i class="fa-solid fa-clipboard-user w-5 text-center text-base {{ request()->routeIs('pikets.*') ? 'text-white' : 'text-indigo-400' }}"></i>
                    <span>Guru Piket Harian</span>
                    <span class="ml-auto bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 text-[10px] font-bold px-2 py-0.5 rounded-full">5 Hari</span>
                </a>

                <div class="pt-4 px-3 pb-2 text-[11px] font-semibold tracking-wider text-slate-400 uppercase">Data Master</div>

                <!-- Guru -->
                <a href="{{ route('teachers.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('teachers.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <i class="fa-solid fa-chalkboard-user w-5 text-center text-base"></i>
                    <span>Data Guru</span>
                    <span class="ml-auto bg-slate-800 text-slate-400 text-xs px-2 py-0.5 rounded-lg">{{ \App\Models\Teacher::count() }}</span>
                </a>

                <!-- Mata Pelajaran -->
                <a href="{{ route('subjects.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('subjects.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <i class="fa-solid fa-book-open-reader w-5 text-center text-base"></i>
                    <span>Mata Pelajaran</span>
                    <span class="ml-auto bg-slate-800 text-slate-400 text-xs px-2 py-0.5 rounded-lg">{{ \App\Models\Subject::count() }}</span>
                </a>

                <!-- Kelas / Rombel -->
                <a href="{{ route('classrooms.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('classrooms.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <i class="fa-solid fa-school w-5 text-center text-base"></i>
                    <span>Kelas (Rombel)</span>
                    <span class="ml-auto bg-slate-800 text-slate-400 text-xs px-2 py-0.5 rounded-lg">{{ \App\Models\Classroom::count() }}</span>
                </a>

                <!-- Distribusi Tugas Mengajar -->
                <a href="{{ route('assignments.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('assignments.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <i class="fa-solid fa-list-check w-5 text-center text-base"></i>
                    <span>Distribusi Tugas</span>
                    <span class="ml-auto bg-slate-800 text-slate-400 text-xs px-2 py-0.5 rounded-lg">{{ \App\Models\TeacherAssignment::count() }}</span>
                </a>

                <div class="pt-4 px-3 pb-2 text-[11px] font-semibold tracking-wider text-slate-400 uppercase">Sistem & Bantuan</div>

                <!-- Profil & Akun Pengguna -->
                <a href="{{ route('profile.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('profile.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30 font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <i class="fa-solid fa-user-gear w-5 text-center text-base {{ request()->routeIs('profile.*') ? 'text-white' : 'text-blue-400' }}"></i>
                    <span>Profil & Akun</span>
                </a>

                <!-- Pengaturan -->
                <a href="{{ route('settings.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('settings.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <i class="fa-solid fa-sliders w-5 text-center text-base"></i>
                    <span>Pengaturan Madrasah</span>
                </a>

                <!-- Buku Panduan PDF -->
                <a href="{{ asset('PANDUAN_PENGGUNAAN_APLIKASI_JADWAL_MAN20.pdf') }}" target="_blank" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium text-amber-300 hover:bg-slate-800 hover:text-amber-200 transition-all">
                    <i class="fa-solid fa-file-pdf w-5 text-center text-base text-amber-400"></i>
                    <span>Buku Panduan PDF</span>
                    <span class="ml-auto bg-amber-500/20 text-amber-300 text-[10px] font-bold px-2 py-0.5 rounded-full border border-amber-500/30">Manual</span>
                </a>
            </nav>

            <!-- User Profile in Sidebar Footer -->
            <div class="p-3.5 border-t border-slate-800 bg-slate-950/50">
                <div class="flex items-center justify-between">
                    <a href="{{ route('profile.index') }}" title="Lihat & Edit Profil Akun" class="flex items-center space-x-3 overflow-hidden group hover:opacity-90 transition">
                        <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-blue-500 to-indigo-600 text-white flex items-center justify-center font-bold text-sm shadow flex-shrink-0 group-hover:ring-2 group-hover:ring-blue-400 transition">
                            {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                        </div>
                        <div class="truncate">
                            <p class="text-xs font-semibold text-white group-hover:text-blue-300 transition truncate">{{ Auth::user()->name ?? 'Admin' }}</p>
                            <p class="text-[10px] text-slate-400 truncate">{{ Auth::user()->email ?? 'admin@man20.sch.id' }}</p>
                        </div>
                    </a>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" title="Logout" class="p-2 text-slate-400 hover:text-rose-400 hover:bg-slate-800 rounded-lg transition-colors">
                            <i class="fa-solid fa-right-from-bracket"></i>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- MAIN CONTENT AREA -->
        <div class="flex-1 flex flex-col min-w-0 h-screen overflow-hidden">
            
            <!-- TOP NAVBAR -->
            <header class="h-16 bg-white border-b border-slate-200 px-3 sm:px-6 flex items-center justify-between z-20 flex-shrink-0">
                
                <!-- Left: Toggle Sidebar Button & School Identity Badge -->
                <div class="flex items-center space-x-2 sm:space-x-3">
                    <!-- Toggle Sidebar Button (Desktop & Mobile) -->
                    <button id="sidebarToggleBtn" 
                            onclick="toggleSidebar()"
                            title="Buka/Tutup Sidebar (Ctrl+B)"
                            class="p-2 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition flex items-center justify-center focus:outline-none focus:ring-2 focus:ring-blue-500/40">
                        <i class="fa-solid fa-bars-staggered text-base sm:text-lg"></i>
                    </button>

                    <!-- School Badges -->
                    <div class="flex items-center space-x-2 overflow-hidden">
                        <span class="inline-flex items-center gap-1.5 px-2.5 sm:px-3 py-1 rounded-full text-[11px] sm:text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 whitespace-nowrap">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            MAN 20 JAKARTA TIMUR
                        </span>
                        <span class="hidden md:inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-600 border border-slate-200 whitespace-nowrap">
                            T.P 2026/2027 (Ganjil)
                        </span>
                    </div>
                </div>

                <!-- Right: Action Buttons -->
                <div class="flex items-center space-x-2 sm:space-x-3">
                    <!-- Quick Generate Button -->
                    <form action="{{ route('schedules.generate') }}" method="POST" onsubmit="return confirm('Generate jadwal otomatis sekarang? Seluruh slot jadwal akan disusun ulang dengan algoritma anti-bentrok.');">
                        @csrf
                        <button type="submit" 
                                title="Generate Jadwal Otomatis"
                                class="inline-flex items-center space-x-1.5 sm:space-x-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white text-xs font-bold px-2.5 sm:px-3.5 py-2 rounded-xl shadow-sm transition-all transform active:scale-95">
                            <i class="fa-solid fa-wand-magic-sparkles text-xs sm:text-sm"></i>
                            <span class="hidden md:inline">Generate Jadwal Otomatis</span>
                        </button>
                    </form>

                    <!-- Print Master Schedule -->
                    <a href="{{ route('schedules.print.all') }}" target="_blank" 
                       title="Cetak Jadwal Pelajaran"
                       class="inline-flex items-center space-x-1 sm:space-x-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold px-2.5 sm:px-3 py-2 rounded-xl transition-all border border-slate-200">
                        <i class="fa-solid fa-print text-xs sm:text-sm"></i>
                        <span class="hidden sm:inline">Cetak Jadwal</span>
                    </a>

                    <!-- Profile Link -->
                    <a href="{{ route('profile.index') }}" 
                       title="Pengaturan Profil & Akun Pengguna"
                       class="inline-flex items-center space-x-1 sm:space-x-1.5 {{ request()->routeIs('profile.*') ? 'bg-blue-600 text-white shadow-sm' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }} text-xs font-semibold px-2.5 sm:px-3 py-2 rounded-xl transition-all border border-slate-200">
                        <i class="fa-solid fa-user-gear text-xs sm:text-sm"></i>
                        <span class="hidden sm:inline">Profil</span>
                    </a>
                </div>
            </header>

            <!-- FLASH NOTIFICATION BANNERS -->
            <div class="px-3 sm:px-6 pt-3">
                @if(session('success'))
                    <div class="mb-3 flex items-center justify-between p-3.5 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800 text-xs sm:text-sm shadow-sm animate-fade-in">
                        <div class="flex items-center space-x-3">
                            <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-circle-check"></i>
                            </div>
                            <span class="font-medium leading-relaxed">{{ session('success') }}</span>
                        </div>
                        <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800 p-1"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-3 flex items-center justify-between p-3.5 bg-rose-50 border border-rose-200 rounded-xl text-rose-800 text-xs sm:text-sm shadow-sm">
                        <div class="flex items-center space-x-3">
                            <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-circle-exclamation"></i>
                            </div>
                            <span class="font-medium leading-relaxed">{{ session('error') }}</span>
                        </div>
                        <button onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-800 p-1"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                @endif
            </div>

            <!-- SCROLLABLE PAGE BODY -->
            <main class="flex-1 overflow-y-auto overflow-x-hidden px-3 sm:px-6 pb-12 pt-2">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- SIDEBAR TOGGLE & RESPONSIVE BEHAVIOR SCRIPT -->
    <script>
        const sidebar = document.getElementById('sidebar');
        const backdrop = document.getElementById('sidebarBackdrop');

        // Check if desktop sidebar was previously collapsed in localStorage
        const isDesktop = () => window.innerWidth >= 1024;

        function initSidebarState() {
            if (isDesktop()) {
                const isCollapsed = localStorage.getItem('sidebar_collapsed') === 'true';
                if (isCollapsed) {
                    sidebar.classList.add('sidebar-collapsed-desktop');
                } else {
                    sidebar.classList.remove('sidebar-collapsed-desktop');
                }
                // Ensure mobile off-canvas classes are reset
                sidebar.classList.remove('-translate-x-full', 'translate-x-0');
                if (backdrop) backdrop.classList.add('hidden');
            } else {
                // Mobile default is off-canvas (-translate-x-full)
                sidebar.classList.remove('sidebar-collapsed-desktop');
                if (!sidebar.classList.contains('translate-x-0')) {
                    sidebar.classList.add('-translate-x-full');
                }
            }
        }

        function toggleSidebar(forceState = null) {
            if (isDesktop()) {
                // Desktop toggle: toggle sidebar-collapsed-desktop class
                const currentlyCollapsed = sidebar.classList.contains('sidebar-collapsed-desktop');
                const willCollapse = forceState !== null ? !forceState : !currentlyCollapsed;

                if (willCollapse) {
                    sidebar.classList.add('sidebar-collapsed-desktop');
                    localStorage.setItem('sidebar_collapsed', 'true');
                } else {
                    sidebar.classList.remove('sidebar-collapsed-desktop');
                    localStorage.setItem('sidebar_collapsed', 'false');
                }
            } else {
                // Mobile toggle: slide in/out with backdrop
                const isMobileOpen = sidebar.classList.contains('translate-x-0');
                const willOpen = forceState !== null ? forceState : !isMobileOpen;

                if (willOpen) {
                    sidebar.classList.remove('-translate-x-full');
                    sidebar.classList.add('translate-x-0');
                    if (backdrop) backdrop.classList.remove('hidden');
                } else {
                    sidebar.classList.remove('translate-x-0');
                    sidebar.classList.add('-translate-x-full');
                    if (backdrop) backdrop.classList.add('hidden');
                }
            }
        }

        // Initialize on load and resize
        window.addEventListener('DOMContentLoaded', initSidebarState);
        window.addEventListener('resize', () => {
            initSidebarState();
        });

        // Keyboard Shortcut: Ctrl+B or Cmd+B to toggle sidebar
        document.addEventListener('keydown', (e) => {
            if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'b') {
                e.preventDefault();
                toggleSidebar();
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
