@extends('layouts.app')

@section('title', 'Analisis Bentrok Jadwal - SIKAD MAN 20 Jakarta')

@section('content')
<div class="space-y-6">

    <!-- TOOLBAR & HEADER -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-amber-100 text-amber-800 border border-amber-200">
                    <i class="fa-solid fa-triangle-exclamation mr-1 text-amber-600"></i> AUDIT JADWAL
                </span>
                <span class="text-xs text-slate-400 font-medium">Deteksi Tumpang Tindih Slot Mengajar</span>
            </div>
            <h2 class="text-xl font-extrabold text-slate-800 tracking-tight mt-1">Laporan Deteksi Jadwal Bentrok</h2>
            <p class="text-xs text-slate-500 mt-0.5">Analisis detail guru yang mengajar di beberapa kelas sekaligus pada hari dan jam yang sama</p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <!-- Back to Schedules -->
            <a href="{{ route('schedules.index') }}" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition border border-slate-200 inline-flex items-center gap-1.5">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Jadwal
            </a>

            <!-- View Master Schedule -->
            <a href="{{ route('schedules.index', ['type' => 'master']) }}" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition border border-slate-200 inline-flex items-center gap-1.5">
                <i class="fa-solid fa-table"></i> Master Matrix
            </a>

            <!-- Regenerate Button -->
            <form action="{{ route('schedules.generate') }}" method="POST" onsubmit="return confirm('Generate ulang jadwal sekarang? Sistem otomatis akan menata ulang slot agar 100% bebas bentrok.');">
                @csrf
                <button type="submit" class="px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white text-xs font-bold rounded-xl shadow-md shadow-blue-500/20 transition inline-flex items-center gap-1.5">
                    <i class="fa-solid fa-wand-magic-sparkles"></i> Susun Ulang Anti-Bentrok
                </button>
            </form>
        </div>
    </div>

    <!-- METRICS CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Titik Bentrok -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Titik Bentrok</p>
                <h3 class="text-2xl font-black text-amber-600 mt-1">{{ $totalClashes }} Slot</h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Tumpang tindih jam mengajar</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-xl border border-amber-200/60">
                <i class="fa-solid fa-bolt-lightning"></i>
            </div>
        </div>

        <!-- Guru Terdampak -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Guru Terdampak</p>
                <h3 class="text-2xl font-black text-slate-800 mt-1">{{ $affectedTeachers }} Guru</h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Memiliki jadwal ganda</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xl border border-blue-200/60">
                <i class="fa-solid fa-chalkboard-user"></i>
            </div>
        </div>

        <!-- Hari Terdeteksi -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Hari Terdeteksi</p>
                <h3 class="text-2xl font-black text-slate-800 mt-1">{{ $affectedDays->count() }} Hari</h3>
                <p class="text-[11px] text-slate-400 mt-0.5">{{ $affectedDays->implode(', ') }}</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-xl border border-indigo-200/60">
                <i class="fa-regular fa-calendar-days"></i>
            </div>
        </div>

        <!-- Sifat Bentrok -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Kegiatan Khusus</p>
                <h3 class="text-2xl font-black text-emerald-600 mt-1">2 Sesi</h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Pramuka Gabungan Kelas X</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-xl border border-emerald-200/60">
                <i class="fa-solid fa-users-rays"></i>
            </div>
        </div>
    </div>

    <!-- INFORMATIONAL NOTICE CALLOUT -->
    <div class="bg-amber-50/80 border border-amber-200 rounded-2xl p-5 shadow-xs flex flex-col md:flex-row items-start gap-4">
        <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center flex-shrink-0 font-bold shadow-sm">
            <i class="fa-solid fa-lightbulb text-lg"></i>
        </div>
        <div class="flex-1 text-xs text-amber-900 leading-relaxed">
            <h4 class="font-extrabold text-sm text-amber-950 mb-1">Penyebab Deteksi Bentrok pada Dokumen SK Resmi (Revisi 4):</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-2">
                <div class="bg-white/80 p-3 rounded-xl border border-amber-200/60">
                    <strong class="text-amber-950 block mb-0.5"><i class="fa-solid fa-circle-check text-emerald-600 mr-1"></i> 1. Kegiatan Bersama (Pramuka Gabungan):</strong>
                    Pada hari <strong>Rabu Jam ke-1 & 2</strong>, seluruh kelas X (X-1 s/d X-5) melaksanakan kegiatan <strong>PRAMUKA</strong> secara serentak di bawah pembina yang sama. Hal ini disengaja sesuai kurikulum.
                </div>
                <div class="bg-white/80 p-3 rounded-xl border border-amber-200/60">
                    <strong class="text-amber-950 block mb-0.5"><i class="fa-solid fa-circle-info text-blue-600 mr-1"></i> 2. Penugasan Paralel / Team Teaching:</strong>
                    Pada lembar SK asli, beberapa guru ditugaskan di 2 rombel pada jam yang sama (misal Guru Lintang di X.1 & XII.2 pada Senin jam 3-4, Guru Feriya, dan Guru Linda).
                </div>
            </div>
        </div>
    </div>

    <!-- FILTER & SEARCH BAR -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <form method="GET" action="{{ route('schedules.conflicts') }}" class="flex flex-wrap items-center gap-2.5 w-full sm:w-auto">
            <label class="text-xs font-bold text-slate-600">Filter Tampilan:</label>
            
            <!-- Filter Hari -->
            <select name="day" onchange="this.form.submit()" class="text-xs font-semibold px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">-- Semua Hari --</option>
                @foreach($days as $d)
                    <option value="{{ $d }}" {{ $selectedDay === $d ? 'selected' : '' }}>Hari {{ $d }}</option>
                @endforeach
            </select>

            <!-- Filter Guru -->
            <select name="teacher_id" onchange="this.form.submit()" class="text-xs font-semibold px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-blue-500 max-w-xs">
                <option value="">-- Semua Guru Terdampak --</option>
                @foreach($allTeachersWithClash as $t)
                    <option value="{{ $t->id }}" {{ $selectedTeacherId == $t->id ? 'selected' : '' }}>
                        {{ $t->name }}
                    </option>
                @endforeach
            </select>

            @if($selectedDay || $selectedTeacherId)
                <a href="{{ route('schedules.conflicts') }}" class="text-xs font-bold text-rose-500 hover:underline px-2 py-1">
                    Reset Filter
                </a>
            @endif
        </form>

        <div class="text-xs text-slate-400 font-medium">
            Menampilkan <strong class="text-slate-800">{{ count($detailedConflicts) }}</strong> titik bentrok
        </div>
    </div>

    <!-- LIST OF DETAILED CONFLICT CARDS -->
    <div class="space-y-4">
        @forelse($detailedConflicts as $index => $c)
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden hover:border-slate-300 transition-all">
                
                <!-- CARD HEADER -->
                <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50/50">
                    <div class="flex items-center space-x-3.5">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-rose-500 to-amber-500 text-white flex items-center justify-center font-black text-sm shadow-sm flex-shrink-0">
                            #{{ $index + 1 }}
                        </div>
                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <h3 class="font-extrabold text-slate-800 text-sm sm:text-base">{{ $c['teacher']->name }}</h3>
                                @if($c['teacher']->order_no)
                                    <span class="px-2 py-0.5 rounded-md bg-slate-200 text-slate-700 text-[10px] font-bold">
                                        Kode: {{ $c['teacher']->order_no }}
                                    </span>
                                @endif
                                @if($c['is_pramuka'])
                                    <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-extrabold border border-emerald-200">
                                        PRAMUKA GABUNGAN
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full bg-rose-100 text-rose-800 text-[10px] font-extrabold border border-rose-200">
                                        BENTROK GANDA ({{ $c['count'] }} KELAS)
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs text-slate-400 mt-0.5">
                                NIP: {{ $c['teacher']->nip ?: '-' }} &bull; Tugas: {{ $c['teacher']->additional_role ?: 'Guru Mapel' }}
                            </p>
                        </div>
                    </div>

                    <!-- TIME & PERIOD BADGE -->
                    <div class="flex items-center gap-2 self-start sm:self-auto">
                        <div class="text-right">
                            <div class="text-xs font-black text-slate-800">
                                Hari {{ $c['day'] }}, Jam Ke-{{ $c['period'] }}
                            </div>
                            <div class="text-[11px] text-slate-500 font-semibold">
                                <i class="fa-regular fa-clock mr-1"></i>{{ $c['time'] }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CARD BODY: SIDE-BY-SIDE CONFLICTING CLASSES -->
                <div class="p-4 sm:p-5">
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">
                        <i class="fa-solid fa-code-compare mr-1 text-blue-600"></i> Kelas & Mata Pelajaran yang Bertabrakan pada Jam Ini:
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-{{ min(count($c['schedules']), 5) }} gap-3">
                        @foreach($c['schedules'] as $slot)
                            <div class="rounded-xl p-3.5 border relative transition hover:shadow-sm"
                                 style="background-color: {{ $slot->subject->color ? $slot->subject->color . '10' : '#f8fafc' }}; border-color: {{ $slot->subject->color ? $slot->subject->color . '40' : '#e2e8f0' }};">
                                
                                <div class="flex items-center justify-between mb-2">
                                    <span class="px-2 py-0.5 rounded-md font-extrabold text-xs text-white shadow-2xs"
                                          style="background-color: {{ $slot->subject->color ?: '#2563eb' }}">
                                        Kelas {{ $slot->classroom->name }}
                                    </span>
                                    <span class="text-[10px] font-semibold text-slate-400">
                                        Tingkat {{ $slot->classroom->grade }}
                                    </span>
                                </div>

                                <div class="font-extrabold text-xs text-slate-800 leading-snug line-clamp-2">
                                    {{ $slot->subject->name }}
                                </div>

                                <div class="mt-2 pt-2 border-t border-slate-200/60 flex items-center justify-between text-[11px] text-slate-500">
                                    <span><i class="fa-solid fa-door-open mr-1 text-slate-400"></i>{{ $slot->room ?: 'R.' . $slot->classroom->name }}</span>
                                    <a href="{{ route('schedules.index', ['type' => 'class', 'classroom_id' => $slot->classroom_id]) }}" 
                                       class="font-bold text-blue-600 hover:text-blue-800 hover:underline">
                                        Buka Jadwal &rarr;
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- RECOMMENDATION -->
                    <div class="mt-4 pt-3 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs">
                        <div class="text-slate-500">
                            @if($c['is_pramuka'])
                                <i class="fa-solid fa-circle-check text-emerald-500 mr-1.5"></i>
                                <span>Status: <strong>Normal</strong> (Pramuka serentak seluruh kelas X sesuai kurikulum).</span>
                            @else
                                <i class="fa-solid fa-triangle-exclamation text-amber-500 mr-1.5"></i>
                                <span>Solusi Rekomendasi: Geser salah satu jam mengajar ke slot kosong, atau klik tombol <em>Generate Ulang Anti-Bentrok</em>.</span>
                            @endif
                        </div>
                        <a href="{{ route('schedules.index', ['type' => 'teacher', 'teacher_id' => $c['teacher']->id]) }}" 
                           class="font-bold text-blue-600 hover:underline whitespace-nowrap">
                            <i class="fa-solid fa-user-pen mr-1"></i> Lihat Semua Jadwal Guru Ini
                        </a>
                    </div>
                </div>

            </div>
        @empty
            <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center shadow-sm">
                <div class="w-16 h-16 rounded-2xl bg-emerald-100 text-emerald-600 mx-auto flex items-center justify-center text-3xl mb-4">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <h3 class="font-extrabold text-slate-800 text-lg">Tidak Ada Jadwal Bentrok!</h3>
                <p class="text-xs text-slate-400 max-w-md mx-auto mt-1">
                    Semua jadwal guru dan kelas telah tersusun dengan rapi tanpa ada bentrok jam mengajar.
                </p>
                <div class="mt-5">
                    <a href="{{ route('schedules.index') }}" class="px-4 py-2 bg-blue-600 text-white font-bold text-xs rounded-xl shadow-sm hover:bg-blue-700 transition">
                        Buka Jadwal Pelajaran
                    </a>
                </div>
            </div>
        @endforelse
    </div>

</div>
@endsection
