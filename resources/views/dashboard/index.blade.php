@extends('layouts.app')

@section('title', 'Dashboard - SIKAD MAN 20 Jakarta')

@section('content')
<div class="space-y-6">

    <!-- WELCOME BANNER -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-slate-900 via-indigo-950 to-blue-900 text-white p-6 md:p-8 shadow-xl">
        <div class="absolute -right-10 -bottom-10 w-72 h-72 bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute right-40 -top-10 w-60 h-60 bg-emerald-500/10 rounded-full blur-2xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-white/10 text-emerald-300 border border-white/10 mb-3 backdrop-blur-sm">
                    <i class="fa-solid fa-graduation-cap"></i> {{ $schoolName }}
                </div>
                <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight text-white leading-tight">
                    Sistem Otomasi Jadwal Pembelajaran
                </h1>
                <p class="text-slate-300 text-xs md:text-sm mt-1 max-w-2xl leading-relaxed">
                    Kelola pembagian beban tugas 36 guru dan 15 rombongan belajar Tahun Pelajaran {{ $academicYear }} Semester {{ $semester }} secara optimal dan bebas bentrok.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <form action="{{ route('schedules.generate') }}" method="POST" onsubmit="return confirm('Mulai proses generate jadwal otomatis untuk seluruh 15 kelas?');">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-bold text-xs shadow-lg shadow-emerald-500/20 transition transform active:scale-95">
                        <i class="fa-solid fa-wand-magic-sparkles text-sm"></i>
                        <span>Generate Jadwal Otomatis</span>
                    </button>
                </form>

                <a href="{{ route('schedules.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white font-semibold text-xs border border-white/20 backdrop-blur-sm transition">
                    <i class="fa-solid fa-calendar-days text-sm"></i>
                    <span>Buka Jadwal</span>
                </a>
            </div>
        </div>
    </div>

    <!-- CONFLICT ALERT NOTIFIER -->
    @if(count($conflicts) === 0 && $totalHoursScheduled > 0)
        <div class="flex items-center gap-3.5 p-4 bg-emerald-50 border border-emerald-200 rounded-2xl text-emerald-800 shadow-sm">
            <div class="w-10 h-10 rounded-xl bg-emerald-500 text-white flex items-center justify-center flex-shrink-0 shadow-md shadow-emerald-500/20">
                <i class="fa-solid fa-shield-check text-lg"></i>
            </div>
            <div class="flex-1">
                <h4 class="text-sm font-bold text-emerald-900">Jadwal 100% Bebas Bentrok (0 Konflik)</h4>
                <p class="text-xs text-emerald-700">Seluruh {{ $totalHoursScheduled }} jam pelajaran telah terverifikasi aman: tidak ada guru yang mengajar di dua kelas bersamaan.</p>
            </div>
            <span class="px-3 py-1 bg-emerald-200 text-emerald-900 text-xs font-bold rounded-full">Optimal</span>
        </div>
    @elseif(count($conflicts) > 0)
        <div class="p-4 bg-amber-50 border border-amber-200 rounded-2xl text-amber-900 shadow-sm">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center flex-shrink-0 shadow-md shadow-amber-500/20">
                    <i class="fa-solid fa-triangle-exclamation text-lg"></i>
                </div>
                <div class="flex-1">
                    <h4 class="text-sm font-bold text-amber-900">Terdeteksi {{ count($conflicts) }} Konflik Jadwal</h4>
                    <p class="text-xs text-amber-800 mb-2">Beberapa guru memiliki jadwal mengajar di kelas berbeda pada hari dan jam yang sama. Tekan tombol generate ulang atau sesuaikan jadwal manual.</p>
                    <div class="space-y-1.5 max-h-32 overflow-y-auto">
                        @foreach($conflicts as $c)
                            <div class="text-xs bg-white/80 p-2 rounded-lg border border-amber-200">
                                <strong>{{ $c['title'] }}</strong> - {{ $c['description'] }}
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- KEY METRICS STATS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Guru -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Guru</p>
                    <h3 class="text-2xl font-extrabold text-slate-800 mt-1">{{ $totalTeachers }} <span class="text-xs font-normal text-slate-400">Orang</span></h3>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-chalkboard-user"></i>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span>Beban >30 JP: <strong class="text-slate-700">{{ $heavyLoadCount }}</strong></span>
                <span>Standar (24-30): <strong class="text-emerald-600">{{ $normalLoadCount }}</strong></span>
            </div>
        </div>

        <!-- Mata Pelajaran -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Mata Pelajaran</p>
                    <h3 class="text-2xl font-extrabold text-slate-800 mt-1">{{ $totalSubjects }} <span class="text-xs font-normal text-slate-400">Mapel</span></h3>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-book-open"></i>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span>Wajib & Tingkat Lanjut</span>
                <span class="text-indigo-600 font-semibold">Kurikulum Merdeka</span>
            </div>
        </div>

        <!-- Rombel / Kelas -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Rombongan Belajar</p>
                    <h3 class="text-2xl font-extrabold text-slate-800 mt-1">{{ $totalClassrooms }} <span class="text-xs font-normal text-slate-400">Kelas</span></h3>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-school"></i>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span>X (5) &bull; XI (5) &bull; XII (5)</span>
                <span class="text-emerald-600 font-semibold">15 Rombel</span>
            </div>
        </div>

        <!-- Total Jam Pelajaran -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Jam Terjadwal</p>
                    <h3 class="text-2xl font-extrabold text-slate-800 mt-1">{{ $totalHoursScheduled }} <span class="text-xs font-normal text-slate-400">/ {{ $totalHoursTarget }} JP</span></h3>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-clock"></i>
                </div>
            </div>
            <div class="mt-3">
                <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-500 to-emerald-500 h-2 rounded-full transition-all duration-500" style="width: {{ $scheduleProgress }}%"></div>
                </div>
                <div class="mt-2 flex items-center justify-between text-[11px] text-slate-500">
                    <span>Progress Kelengkapan</span>
                    <span class="font-bold text-slate-700">{{ $scheduleProgress }}%</span>
                </div>
            </div>
        </div>
    </div>

    <!-- CHARTS ROW -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Beban Mengajar Guru Chart -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm lg:col-span-1">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-sm text-slate-800">Distribusi Beban Jam Guru</h3>
                <span class="text-xs text-slate-400">36 Guru</span>
            </div>
            <div class="relative h-60 flex items-center justify-center">
                <canvas id="teacherLoadChart"></canvas>
            </div>
            <div class="mt-4 grid grid-cols-3 gap-2 text-center text-xs">
                <div class="bg-rose-50 p-2 rounded-xl border border-rose-100">
                    <div class="font-bold text-rose-700">{{ $heavyLoadCount }}</div>
                    <div class="text-[10px] text-rose-500">>30 Jam</div>
                </div>
                <div class="bg-emerald-50 p-2 rounded-xl border border-emerald-100">
                    <div class="font-bold text-emerald-700">{{ $normalLoadCount }}</div>
                    <div class="text-[10px] text-emerald-500">24-30 Jam</div>
                </div>
                <div class="bg-blue-50 p-2 rounded-xl border border-blue-100">
                    <div class="font-bold text-blue-700">{{ $lightLoadCount }}</div>
                    <div class="text-[10px] text-blue-500"><24 Jam</div>
                </div>
            </div>
        </div>

        <!-- Komposisi Mapel Chart -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm lg:col-span-2">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-sm text-slate-800">Komposisi Kategori Mata Pelajaran</h3>
                <span class="text-xs text-slate-400">Total 37 Mapel</span>
            </div>
            <div class="relative h-60">
                <canvas id="subjectCategoryChart"></canvas>
            </div>
            <div class="mt-4 flex flex-wrap gap-2 text-xs">
                @foreach($subjectsByCategory as $cat => $cnt)
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700">
                        <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                        {{ $cat }}: <strong>{{ $cnt }}</strong>
                    </span>
                @endforeach
            </div>
        </div>
    </div>

    <!-- LOWER SECTION: TOP TEACHERS & CLASSROOM STATUS -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- TOP TEACHERS -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm lg:col-span-2">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="font-bold text-sm text-slate-800">Guru dengan Beban Mengajar Terbanyak</h3>
                    <p class="text-xs text-slate-500">Berdasarkan pembagian tugas mengajar mingguan</p>
                </div>
                <a href="{{ route('teachers.index') }}" class="text-xs text-blue-600 hover:text-blue-700 font-semibold">
                    Lihat Semua <i class="fa-solid fa-arrow-right ml-1"></i>
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-slate-400 border-b border-slate-100 uppercase text-[10px]">
                            <th class="py-2.5 px-3">No</th>
                            <th class="py-2.5 px-3">Nama Guru</th>
                            <th class="py-2.5 px-3">Tugas Tambahan</th>
                            <th class="py-2.5 px-3 text-right">Jam Mengajar</th>
                            <th class="py-2.5 px-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($topTeachers as $idx => $t)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-3 px-3 font-semibold text-slate-400">{{ $idx + 1 }}</td>
                                <td class="py-3 px-3">
                                    <div class="font-bold text-slate-800">{{ $t->name }}</div>
                                    <div class="text-[10px] text-slate-400">No Urut: {{ $t->order_no ?? '-' }}</div>
                                </td>
                                <td class="py-3 px-3">
                                    @if($t->additional_role)
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                            {{ Str::limit($t->additional_role, 30) }}
                                        </span>
                                    @else
                                        <span class="text-slate-300">-</span>
                                    @endif
                                </td>
                                <td class="py-3 px-3 text-right font-extrabold text-blue-600">
                                    {{ $t->total_hours }} JP
                                </td>
                                <td class="py-3 px-3 text-right">
                                    <a href="{{ route('schedules.index', ['type' => 'teacher', 'teacher_id' => $t->id]) }}" class="px-2.5 py-1 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-lg font-semibold text-[11px] transition">
                                        Jadwal
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 15 ROMBEL STATUS -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm lg:col-span-1">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="font-bold text-sm text-slate-800">Status 15 Rombel</h3>
                    <p class="text-xs text-slate-500">Kelengkapan jadwal per kelas</p>
                </div>
                <a href="{{ route('classrooms.index') }}" class="text-xs text-blue-600 hover:text-blue-700 font-semibold">
                    Kelola <i class="fa-solid fa-arrow-right ml-1"></i>
                </a>
            </div>

            <div class="space-y-2.5 max-h-[380px] overflow-y-auto pr-1">
                @foreach($classrooms as $cls)
                    <div class="p-2.5 rounded-xl border border-slate-100 bg-slate-50 hover:bg-white hover:border-slate-300 transition flex items-center justify-between">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-xs text-slate-800">Kelas {{ $cls->name }}</span>
                                <span class="text-[10px] px-1.5 py-0.2 rounded bg-slate-200 text-slate-600 font-medium">Tingkat {{ $cls->grade }}</span>
                            </div>
                            <div class="text-[11px] text-slate-500 mt-0.5">
                                Terjadwal: <strong class="text-slate-700">{{ $cls->schedules_count }}</strong> / 50 JP
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            @if($cls->schedules_count >= 48)
                                <span class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-xs" title="Lengkap">
                                    <i class="fa-solid fa-check"></i>
                                </span>
                            @else
                                <span class="w-6 h-6 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center text-xs" title="Sebagian">
                                    <i class="fa-solid fa-minus"></i>
                                </span>
                            @endif
                            <a href="{{ route('schedules.index', ['type' => 'class', 'classroom_id' => $cls->id]) }}" class="text-slate-400 hover:text-blue-600 p-1">
                                <i class="fa-solid fa-chevron-right text-xs"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // 1. Teacher Load Donut Chart
        const ctxTeacher = document.getElementById('teacherLoadChart').getContext('2d');
        new Chart(ctxTeacher, {
            type: 'doughnut',
            data: {
                labels: ['Beban Tinggi (>30 JP)', 'Standar (24-30 JP)', 'Ringan (<24 JP)'],
                datasets: [{
                    data: [{{ $heavyLoadCount }}, {{ $normalLoadCount }}, {{ $lightLoadCount }}],
                    backgroundColor: ['#f43f5e', '#10b981', '#3b82f6'],
                    borderWidth: 0,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 12, font: { size: 10, family: 'Plus Jakarta Sans' } }
                    }
                },
                cutout: '70%'
            }
        });

        // 2. Subject Category Bar Chart
        const ctxSubject = document.getElementById('subjectCategoryChart').getContext('2d');
        const subjectCats = {!! json_encode(array_keys($subjectsByCategory)) !!};
        const subjectCounts = {!! json_encode(array_values($subjectsByCategory)) !!};

        new Chart(ctxSubject, {
            type: 'bar',
            data: {
                labels: subjectCats,
                datasets: [{
                    label: 'Jumlah Mata Pelajaran',
                    data: subjectCounts,
                    backgroundColor: '#6366f1',
                    borderRadius: 8,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, font: { size: 10, family: 'Plus Jakarta Sans' } },
                        grid: { color: '#f1f5f9' }
                    },
                    x: {
                        ticks: { font: { size: 11, family: 'Plus Jakarta Sans' } },
                        grid: { display: false }
                    }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    });
</script>
@endpush
