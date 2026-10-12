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
    </style>
    @stack('styles')
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen flex flex-col">

    <div class="flex h-screen overflow-hidden">
        <!-- SIDEBAR -->
        <aside id="sidebar" class="w-72 bg-slate-900 text-white flex flex-col flex-shrink-0 transition-all duration-300 z-30 shadow-xl">
            <!-- Brand / Logo -->
            <div class="p-5 border-b border-slate-800 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-emerald-500 to-blue-600 flex items-center justify-center text-white shadow-lg shadow-blue-500/30">
                        <i class="fa-solid fa-calendar-check text-lg"></i>
                    </div>
                    <div>
                        <h1 class="font-bold text-base tracking-tight leading-tight text-white">SIKAD MAN 20</h1>
                        <p class="text-[11px] text-slate-400 font-medium">Jadwal Pelajaran & Guru</p>
                    </div>
                </div>
                <button id="closeSidebarBtn" class="lg:hidden text-slate-400 hover:text-white">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 overflow-y-auto px-4 py-5 space-y-1.5">
                <div class="px-3 pb-2 text-[11px] font-semibold tracking-wider text-slate-400 uppercase">Menu Utama</div>

                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('dashboard') || request()->routeIs('home') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <i class="fa-solid fa-gauge-high w-5 text-center text-base"></i>
                    <span>Dashboard</span>
                </a>

                <!-- Penjadwalan -->
                <a href="{{ route('schedules.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('schedules.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <i class="fa-solid fa-table-cells w-5 text-center text-base"></i>
                    <span>Jadwal Pelajaran</span>
                    @php $conflictCount = \App\Models\Schedule::count() > 0 ? (new \App\Services\ScheduleGeneratorService())->detectConflicts() : []; @endphp
                    @if(count($conflictCount) > 0)
                        <span class="ml-auto bg-amber-500 text-slate-900 text-[10px] font-bold px-2 py-0.5 rounded-full">{{ count($conflictCount) }}</span>
                    @endif
                </a>

                <div class="pt-5 px-3 pb-2 text-[11px] font-semibold tracking-wider text-slate-400 uppercase">Data Master</div>

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

                <div class="pt-5 px-3 pb-2 text-[11px] font-semibold tracking-wider text-slate-400 uppercase">Sistem</div>

                <!-- Pengaturan -->
                <a href="{{ route('settings.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('settings.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <i class="fa-solid fa-sliders w-5 text-center text-base"></i>
                    <span>Pengaturan Madrasah</span>
                </a>
            </nav>

            <!-- User Footer in Sidebar -->
            <div class="p-4 border-t border-slate-800 bg-slate-950/50">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3 overflow-hidden">
                        <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-blue-500 to-indigo-600 text-white flex items-center justify-center font-bold text-sm shadow">
                            {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                        </div>
                        <div class="truncate">
                            <p class="text-xs font-semibold text-white truncate">{{ Auth::user()->name ?? 'Admin' }}</p>
                            <p class="text-[10px] text-slate-400 truncate">{{ Auth::user()->email ?? 'admin@man20.sch.id' }}</p>
                        </div>
                    </div>
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
        <div class="flex-1 flex flex-col h-screen overflow-hidden">
            <!-- TOP NAVBAR -->
            <header class="h-16 bg-white border-b border-slate-200 px-6 flex items-center justify-between z-20 flex-shrink-0">
                <div class="flex items-center space-x-4">
                    <button id="openSidebarBtn" class="lg:hidden text-slate-600 hover:text-slate-900 p-2 rounded-lg hover:bg-slate-100">
                        <i class="fa-solid fa-bars text-lg"></i>
                    </button>
                    <div class="flex items-center space-x-2">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            MAN 20 JAKARTA TIMUR
                        </span>
                        <span class="hidden md:inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-600 border border-slate-200">
                            T.P 2026/2027 (Ganjil)
                        </span>
                    </div>
                </div>

                <div class="flex items-center space-x-3">
                    <!-- Quick Generate Button -->
                    <form action="{{ route('schedules.generate') }}" method="POST" onsubmit="return confirm('Generate jadwal otomatis sekarang? Seluruh slot jadwal akan disusun ulang dengan algoritma anti-bentrok.');">
                        @csrf
                        <button type="submit" class="inline-flex items-center space-x-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white text-xs font-bold px-3.5 py-2 rounded-xl shadow-sm transition-all transform active:scale-95">
                            <i class="fa-solid fa-wand-magic-sparkles"></i>
                            <span class="hidden sm:inline">Generate Jadwal Otomatis</span>
                        </button>
                    </form>

                    <!-- Print Master Schedule -->
                    <a href="{{ route('schedules.print.all') }}" target="_blank" class="inline-flex items-center space-x-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold px-3 py-2 rounded-xl transition-all border border-slate-200">
                        <i class="fa-solid fa-print"></i>
                        <span class="hidden sm:inline">Cetak Jadwal</span>
                    </a>
                </div>
            </header>

            <!-- NOTIFICATION BANNERS -->
            <div class="px-6 pt-4">
                @if(session('success'))
                    <div class="mb-3 flex items-center justify-between p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800 text-sm shadow-sm animate-fade-in">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-circle-check"></i>
                            </div>
                            <span class="font-medium">{{ session('success') }}</span>
                        </div>
                        <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-3 flex items-center justify-between p-4 bg-rose-50 border border-rose-200 rounded-xl text-rose-800 text-sm shadow-sm">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-circle-exclamation"></i>
                            </div>
                            <span class="font-medium">{{ session('error') }}</span>
                        </div>
                        <button onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-800"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                @endif
            </div>

            <!-- SCROLLABLE PAGE BODY -->
            <main class="flex-1 overflow-y-auto px-6 pb-12 pt-2">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Mobile Sidebar Backdrop & Toggle Script -->
    <script>
        const sidebar = document.getElementById('sidebar');
        const openBtn = document.getElementById('openSidebarBtn');
        const closeBtn = document.getElementById('closeSidebarBtn');

        if (openBtn && closeBtn && sidebar) {
            openBtn.addEventListener('click', () => {
                sidebar.classList.remove('-translate-x-full');
            });
            closeBtn.addEventListener('click', () => {
                sidebar.classList.add('-translate-x-full');
            });
        }
    </script>
    @stack('scripts')
</body>
</html>
