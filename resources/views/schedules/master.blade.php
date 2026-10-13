@extends('layouts.app')

@section('title', 'Master Jadwal Resmi - SIKAD MAN 20 Jakarta')

@section('content')
<div class="space-y-6">

    <!-- TOOLBAR -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200">
                    SK RESMI AKTIF
                </span>
                <span class="text-xs text-slate-400 font-medium">{{ $officialHeader['revision'] ?? 'Revisi 4 Berlaku Mulai 12 Oktober 2026' }}</span>
            </div>
            <h2 class="text-xl font-extrabold text-slate-800 tracking-tight mt-1">Master Jadwal Pelajaran Madrasah</h2>
            <p class="text-xs text-slate-500 mt-0.5">Jadwal lengkap seluruh rombel X-1 s/d XII-5, distribusi tugas 36 guru, dan daftar wali kelas</p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <!-- View Mode Switcher -->
            <div class="inline-flex p-1 bg-slate-100 rounded-xl border border-slate-200 text-xs font-semibold">
                <a href="{{ route('schedules.index', ['type' => 'class']) }}" class="px-3 py-1.5 rounded-lg text-slate-600 hover:text-slate-900 transition">
                    <i class="fa-solid fa-users mr-1"></i> Per Kelas
                </a>
                <a href="{{ route('schedules.index', ['type' => 'teacher']) }}" class="px-3 py-1.5 rounded-lg text-slate-600 hover:text-slate-900 transition">
                    <i class="fa-solid fa-user-tie mr-1"></i> Per Guru
                </a>
                <a href="{{ route('schedules.index', ['type' => 'master']) }}" class="px-3 py-1.5 rounded-lg bg-white text-blue-600 shadow-sm transition">
                    <i class="fa-solid fa-table mr-1"></i> Master Matrix
                </a>
            </div>

            <!-- Print Master Schedule Button -->
            <a href="{{ route('schedules.print.all') }}" target="_blank" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl transition shadow-md shadow-blue-500/20 inline-flex items-center gap-1.5">
                <i class="fa-solid fa-print"></i> Cetak Jadwal Pelajaran
            </a>
        </div>
    </div>

    <!-- TAB SELECTOR: OFFICIAL SK vs INTERACTIVE PER HARI -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-200 pb-3">
        <div class="inline-flex p-1 bg-slate-200/80 rounded-xl text-xs font-bold gap-1">
            <a href="{{ route('schedules.index', ['type' => 'master', 'tab' => 'official']) }}" 
               class="px-4 py-2 rounded-lg transition {{ ($activeTab ?? 'official') === 'official' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                <i class="fa-solid fa-file-contract mr-1.5 text-blue-600"></i> Format Resmi SK (Revisi 4)
            </a>
            <a href="{{ route('schedules.index', ['type' => 'master', 'tab' => 'interactive', 'day' => $selectedDay ?? 'Senin']) }}" 
               class="px-4 py-2 rounded-lg transition {{ ($activeTab ?? 'official') === 'interactive' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                <i class="fa-solid fa-calendar-day mr-1.5 text-emerald-600"></i> Matriks Interaktif Harian
            </a>
        </div>

        @if(($activeTab ?? 'official') === 'interactive')
            <!-- DAY SELECTOR TABS -->
            <div class="flex items-center gap-1.5 overflow-x-auto">
                @foreach($days as $day)
                    <a href="{{ route('schedules.index', ['type' => 'master', 'tab' => 'interactive', 'day' => $day]) }}" 
                       class="px-3.5 py-1.5 rounded-lg font-bold text-xs transition whitespace-nowrap {{ $selectedDay === $day ? 'bg-slate-900 text-white' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
                        Hari {{ $day }}
                    </a>
                @endforeach
            </div>
        @else
            <div class="text-xs text-slate-500 flex items-center gap-2">
                <i class="fa-solid fa-circle-info text-blue-500"></i>
                <span>Tampilan 1:1 identik dengan lembar resmi MAN 20 Jakarta</span>
            </div>
        @endif
    </div>

    @if(($activeTab ?? 'official') === 'official')
        <!-- OFFICIAL SCHEDULE VIEW (1:1 FIDELITY CONTAINER) -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-md p-6 overflow-hidden">
            
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-lg">
                        <i class="fa-solid fa-award"></i>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-slate-800 text-sm">Pratinjau Dokumen Jadwal Lengkap</h3>
                        <p class="text-[11px] text-slate-400">Gunakan scroll horizontal atau klik tombol cetak untuk tampilan dokumen</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('schedules.print.all') }}" target="_blank" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-bold border border-slate-200 inline-flex items-center gap-1.5">
                        <i class="fa-solid fa-up-right-from-square"></i> Buka di Tab Baru
                    </a>
                </div>
            </div>

            <!-- EMBEDDED SHEET WRAPPER WITH HORIZONTAL SCROLL -->
            <div class="overflow-x-auto pb-4" style="max-height: 850px; overflow-y: auto;">
                <div style="min-width: 1100px; background: #fff; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px;">
                    
                    <!-- KOP / HEADER -->
                    <div style="position: relative; text-align: center; margin-bottom: 6px; padding: 4px 0;">
                        <img src="{{ asset('images/logo_kemenag.png') }}" alt="Kemenag" style="position: absolute; left: 10px; top: 0; height: 50px;">
                        <img src="{{ asset('images/logo_man20.png') }}" alt="MAN 20" style="position: absolute; right: 10px; top: 0; height: 50px;">
                        <div style="font-size: 15px; font-weight: 900; letter-spacing: 0.5px; text-transform: uppercase;">
                            {{ $officialHeader['title'] ?? 'JADWAL PELAJARAN SEMESTER GANJIL' }}
                        </div>
                        <div style="font-size: 16px; font-weight: 900; letter-spacing: 0.5px; text-transform: uppercase; margin: 2px 0;">
                            {{ $officialHeader['school_name'] ?? 'MAN 20 JAKARTA TIMUR' }}
                        </div>
                        <div style="font-size: 14px; font-weight: 800; letter-spacing: 0.5px;">
                            {{ $officialHeader['academic_year'] ?? 'TAHUN PELAJARAN 2026-2027' }}
                        </div>
                    </div>

                    <div style="font-size: 9.5px; font-weight: 900; text-transform: uppercase; margin-bottom: 5px; color: #000;">
                        {{ $officialHeader['revision'] ?? 'REVISI4_BERLAKU MULAI : Senin, 12 Oktober 2026' }}
                    </div>

                    <!-- TWO-COLUMN LAYOUT -->
                    <div style="display: flex; gap: 10px; align-items: flex-start; width: 100%;">
                        
                        <!-- LEFT COLUMN: TIMETABLE -->
                        <div style="flex: 1 1 63%; max-width: 63%;">
                            <table style="width: 100%; border-collapse: collapse; border: 1px solid #000; font-size: 8.5px; font-family: Arial, sans-serif;">
                                <thead>
                                    <tr style="background: #fff;">
                                        <th rowspan="2" style="border: 1px solid #000; padding: 3px 1px; width: 20px; font-weight: 900;">Hari</th>
                                        <th rowspan="2" style="border: 1px solid #000; padding: 3px 1px; width: 20px; font-weight: 900;">Jam<br>Ke</th>
                                        <th rowspan="2" style="border: 1px solid #000; padding: 3px 2px; width: 68px; font-weight: 900;">Waktu</th>
                                        <th colspan="5" style="border: 1px solid #000; padding: 3px 1px; font-weight: 900;">KELAS X</th>
                                        <th colspan="5" style="border: 1px solid #000; padding: 3px 1px; font-weight: 900;">KELAS XI</th>
                                        <th colspan="5" style="border: 1px solid #000; padding: 3px 1px; font-weight: 900;">KELAS XII</th>
                                    </tr>
                                    <tr style="background: #fff;">
                                        @foreach(['X-1', 'X-2', 'X-3', 'X-4', 'X-5', 'XI-1', 'XI-2', 'XI-3', 'XI-4', 'XI-5', 'XII-1', 'XII-2', 'XII-3', 'XII-4', 'XII-5'] as $cls)
                                            <th style="border: 1px solid #000; padding: 3px 1px; font-weight: 900;">{{ $cls }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $dayLabels = [
                                            'Senin' => "S<br>E<br>N<br>I<br>N",
                                            'Selasa' => "S<br>E<br>L<br>A<br>S<br>A",
                                            'Rabu' => "R<br>A<br>B<br>U",
                                            'Kamis' => "K<br>A<br>M<br>I<br>S",
                                            'Jumat' => "J<br>U<br>M<br>A<br>T",
                                        ];
                                    @endphp

                                    @foreach($officialDaysSchedule as $dayName => $dayInfo)
                                        @php $totalRows = 14; @endphp

                                        <!-- JAM 1 -->
                                        <tr>
                                            <td rowspan="{{ $totalRows }}" style="border: 1px solid #000; text-align: center; font-weight: 900; font-size: 13px; letter-spacing: 1px; line-height: 1.15; width: 20px;">
                                                {!! $dayLabels[$dayName] !!}
                                            </td>
                                            <td style="border: 1px solid #000; text-align: center; font-weight: bold;">1</td>
                                            <td style="border: 1px solid #000; text-align: center; white-space: nowrap;">{{ $dayInfo['periods'][1]['time'] }}</td>
                                            @if($dayName === 'Rabu' && isset($dayInfo['periods'][1]['special_merge_x']))
                                                <td rowspan="2" colspan="5" style="border: 1px solid #000; text-align: center; font-weight: 900; font-size: 10px; letter-spacing: 1px;">PRAMUKA</td>
                                                @foreach(['XI-1', 'XI-2', 'XI-3', 'XI-4', 'XI-5', 'XII-1', 'XII-2', 'XII-3', 'XII-4', 'XII-5'] as $cls)
                                                    <td style="border: 1px solid #000; text-align: center; font-weight: bold;">{{ $dayInfo['periods'][1]['slots'][$cls] ?? '' }}</td>
                                                @endforeach
                                            @else
                                                @foreach(['X-1', 'X-2', 'X-3', 'X-4', 'X-5', 'XI-1', 'XI-2', 'XI-3', 'XI-4', 'XI-5', 'XII-1', 'XII-2', 'XII-3', 'XII-4', 'XII-5'] as $cls)
                                                    <td style="border: 1px solid #000; text-align: center; font-weight: bold;">{{ $dayInfo['periods'][1]['slots'][$cls] ?? '' }}</td>
                                                @endforeach
                                            @endif
                                        </tr>

                                        <!-- JAM 2 -->
                                        <tr>
                                            <td style="border: 1px solid #000; text-align: center; font-weight: bold;">2</td>
                                            <td style="border: 1px solid #000; text-align: center; white-space: nowrap;">{{ $dayInfo['periods'][2]['time'] }}</td>
                                            @if($dayName === 'Rabu' && isset($dayInfo['periods'][2]['special_merge_x']))
                                                @foreach(['XI-1', 'XI-2', 'XI-3', 'XI-4', 'XI-5', 'XII-1', 'XII-2', 'XII-3', 'XII-4', 'XII-5'] as $cls)
                                                    <td style="border: 1px solid #000; text-align: center; font-weight: bold;">{{ $dayInfo['periods'][2]['slots'][$cls] ?? '' }}</td>
                                                @endforeach
                                            @else
                                                @foreach(['X-1', 'X-2', 'X-3', 'X-4', 'X-5', 'XI-1', 'XI-2', 'XI-3', 'XI-4', 'XI-5', 'XII-1', 'XII-2', 'XII-3', 'XII-4', 'XII-5'] as $cls)
                                                    <td style="border: 1px solid #000; text-align: center; font-weight: bold;">{{ $dayInfo['periods'][2]['slots'][$cls] ?? '' }}</td>
                                                @endforeach
                                            @endif
                                        </tr>

                                        <!-- JAM 3 -->
                                        <tr>
                                            <td style="border: 1px solid #000; text-align: center; font-weight: bold;">3</td>
                                            <td style="border: 1px solid #000; text-align: center; white-space: nowrap;">{{ $dayInfo['periods'][3]['time'] }}</td>
                                            @foreach(['X-1', 'X-2', 'X-3', 'X-4', 'X-5', 'XI-1', 'XI-2', 'XI-3', 'XI-4', 'XI-5', 'XII-1', 'XII-2', 'XII-3', 'XII-4', 'XII-5'] as $cls)
                                                <td style="border: 1px solid #000; text-align: center; font-weight: bold;">{{ $dayInfo['periods'][3]['slots'][$cls] ?? '' }}</td>
                                            @endforeach
                                        </tr>

                                        <!-- JAM 4 -->
                                        <tr>
                                            <td style="border: 1px solid #000; text-align: center; font-weight: bold;">4</td>
                                            <td style="border: 1px solid #000; text-align: center; white-space: nowrap;">{{ $dayInfo['periods'][4]['time'] }}</td>
                                            @foreach(['X-1', 'X-2', 'X-3', 'X-4', 'X-5', 'XI-1', 'XI-2', 'XI-3', 'XI-4', 'XI-5', 'XII-1', 'XII-2', 'XII-3', 'XII-4', 'XII-5'] as $cls)
                                                <td style="border: 1px solid #000; text-align: center; font-weight: bold;">{{ $dayInfo['periods'][4]['slots'][$cls] ?? '' }}</td>
                                            @endforeach
                                        </tr>

                                        <!-- BREAK 1 -->
                                        <tr>
                                            <td style="border: 1px solid #000;"></td>
                                            <td style="border: 1px solid #000; text-align: center; white-space: nowrap;">{{ $dayInfo['periods']['break_1']['time'] }}</td>
                                            <td colspan="15" style="border: 1px solid #000; text-align: center; font-weight: 900; letter-spacing: 0.5px;">{{ $dayInfo['periods']['break_1']['label'] }}</td>
                                        </tr>

                                        <!-- JAM 5 -->
                                        <tr>
                                            <td style="border: 1px solid #000; text-align: center; font-weight: bold;">5</td>
                                            <td style="border: 1px solid #000; text-align: center; white-space: nowrap;">{{ $dayInfo['periods'][5]['time'] }}</td>
                                            @foreach(['X-1', 'X-2', 'X-3', 'X-4', 'X-5', 'XI-1', 'XI-2', 'XI-3', 'XI-4', 'XI-5', 'XII-1', 'XII-2', 'XII-3', 'XII-4', 'XII-5'] as $cls)
                                                <td style="border: 1px solid #000; text-align: center; font-weight: bold;">{{ $dayInfo['periods'][5]['slots'][$cls] ?? '' }}</td>
                                            @endforeach
                                        </tr>

                                        <!-- JAM 6 -->
                                        <tr>
                                            <td style="border: 1px solid #000; text-align: center; font-weight: bold;">6</td>
                                            <td style="border: 1px solid #000; text-align: center; white-space: nowrap;">{{ $dayInfo['periods'][6]['time'] }}</td>
                                            @foreach(['X-1', 'X-2', 'X-3', 'X-4', 'X-5', 'XI-1', 'XI-2', 'XI-3', 'XI-4', 'XI-5', 'XII-1', 'XII-2', 'XII-3', 'XII-4', 'XII-5'] as $cls)
                                                <td style="border: 1px solid #000; text-align: center; font-weight: bold;">{{ $dayInfo['periods'][6]['slots'][$cls] ?? '' }}</td>
                                            @endforeach
                                        </tr>

                                        @if($dayName !== 'Jumat')
                                            <!-- JAM 7 -->
                                            <tr>
                                                <td style="border: 1px solid #000; text-align: center; font-weight: bold;">7</td>
                                                <td style="border: 1px solid #000; text-align: center; white-space: nowrap;">{{ $dayInfo['periods'][7]['time'] }}</td>
                                                @foreach(['X-1', 'X-2', 'X-3', 'X-4', 'X-5', 'XI-1', 'XI-2', 'XI-3', 'XI-4', 'XI-5', 'XII-1', 'XII-2', 'XII-3', 'XII-4', 'XII-5'] as $cls)
                                                    <td style="border: 1px solid #000; text-align: center; font-weight: bold;">{{ $dayInfo['periods'][7]['slots'][$cls] ?? '' }}</td>
                                                @endforeach
                                            </tr>

                                            <!-- BREAK 2 -->
                                            <tr>
                                                <td style="border: 1px solid #000;"></td>
                                                <td style="border: 1px solid #000; text-align: center; white-space: nowrap;">{{ $dayInfo['periods']['break_2']['time'] }}</td>
                                                <td colspan="15" style="border: 1px solid #000; text-align: center; font-weight: 900; letter-spacing: 0.5px;">{{ $dayInfo['periods']['break_2']['label'] }}</td>
                                            </tr>
                                        @else
                                            <!-- BREAK 2 JUMAT -->
                                            <tr>
                                                <td style="border: 1px solid #000;"></td>
                                                <td style="border: 1px solid #000; text-align: center; white-space: nowrap;">{{ $dayInfo['periods']['break_2']['time'] }}</td>
                                                <td colspan="15" style="border: 1px solid #000; text-align: center; font-weight: 900; letter-spacing: 0.5px;">{{ $dayInfo['periods']['break_2']['label'] }}</td>
                                            </tr>

                                            <!-- JAM 7 JUMAT -->
                                            <tr>
                                                <td style="border: 1px solid #000; text-align: center; font-weight: bold;">7</td>
                                                <td style="border: 1px solid #000; text-align: center; white-space: nowrap;">{{ $dayInfo['periods'][7]['time'] }}</td>
                                                @foreach(['X-1', 'X-2', 'X-3', 'X-4', 'X-5', 'XI-1', 'XI-2', 'XI-3', 'XI-4', 'XI-5', 'XII-1', 'XII-2', 'XII-3', 'XII-4', 'XII-5'] as $cls)
                                                    <td style="border: 1px solid #000; text-align: center; font-weight: bold;">{{ $dayInfo['periods'][7]['slots'][$cls] ?? '' }}</td>
                                                @endforeach
                                            </tr>
                                        @endif

                                        <!-- JAM 8 -->
                                        <tr>
                                            <td style="border: 1px solid #000; text-align: center; font-weight: bold;">8</td>
                                            <td style="border: 1px solid #000; text-align: center; white-space: nowrap;">{{ $dayInfo['periods'][8]['time'] }}</td>
                                            @foreach(['X-1', 'X-2', 'X-3', 'X-4', 'X-5', 'XI-1', 'XI-2', 'XI-3', 'XI-4', 'XI-5', 'XII-1', 'XII-2', 'XII-3', 'XII-4', 'XII-5'] as $cls)
                                                <td style="border: 1px solid #000; text-align: center; font-weight: bold;">{{ $dayInfo['periods'][8]['slots'][$cls] ?? '' }}</td>
                                            @endforeach
                                        </tr>

                                        <!-- JAM 9 -->
                                        <tr>
                                            <td style="border: 1px solid #000; text-align: center; font-weight: bold;">9</td>
                                            <td style="border: 1px solid #000; text-align: center; white-space: nowrap;">{{ $dayInfo['periods'][9]['time'] }}</td>
                                            @foreach(['X-1', 'X-2', 'X-3', 'X-4', 'X-5', 'XI-1', 'XI-2', 'XI-3', 'XI-4', 'XI-5', 'XII-1', 'XII-2', 'XII-3', 'XII-4', 'XII-5'] as $cls)
                                                <td style="border: 1px solid #000; text-align: center; font-weight: bold;">{{ $dayInfo['periods'][9]['slots'][$cls] ?? '' }}</td>
                                            @endforeach
                                        </tr>

                                        <!-- JAM 10 -->
                                        <tr>
                                            <td style="border: 1px solid #000; text-align: center; font-weight: bold;">10</td>
                                            <td style="border: 1px solid #000; text-align: center; white-space: nowrap;">{{ $dayInfo['periods'][10]['time'] }}</td>
                                            @foreach(['X-1', 'X-2', 'X-3', 'X-4', 'X-5', 'XI-1', 'XI-2', 'XI-3', 'XI-4', 'XI-5', 'XII-1', 'XII-2', 'XII-3', 'XII-4', 'XII-5'] as $cls)
                                                <td style="border: 1px solid #000; text-align: center; font-weight: bold;">{{ $dayInfo['periods'][10]['slots'][$cls] ?? '' }}</td>
                                            @endforeach
                                        </tr>

                                        <!-- SALAT ASHAR -->
                                        <tr>
                                            <td colspan="2" style="border: 1px solid #000; text-align: center; font-size: 8px; font-weight: bold;">{{ $dayInfo['periods']['salat_ashar']['time'] }}</td>
                                            <td colspan="15" style="border: 1px solid #000; text-align: center; font-weight: 900;">{{ $dayInfo['periods']['salat_ashar']['label'] }}</td>
                                        </tr>

                                        <!-- PIKET ROW -->
                                        <tr>
                                            <td colspan="2" style="border: 1px solid #000; text-align: center; font-weight: 900;">PIKET</td>
                                            <td colspan="15" style="border: 1px solid #000; text-align: left; padding-left: 6px; font-weight: 900; font-size: 8px;">{{ $dayInfo['piket'] }}</td>
                                        </tr>

                                    @endforeach
                                </tbody>
                            </table>

                            <div style="font-size: 8px; font-style: italic; margin-top: 4px;">
                                {{ $officialHeader['note'] ?? 'Catatan: Setelah salat Ashar piket kelas wajib membersihkan kelasnya sebelum pulang' }}
                            </div>
                        </div>

                        <!-- RIGHT COLUMN: TEACHERS & WALI KELAS & SIGNATURES -->
                        <div style="flex: 1 1 37%; max-width: 37%;">
                            
                            <!-- TABLE 1: TEACHERS LIST -->
                            <table style="width: 100%; border-collapse: collapse; border: 1px solid #000; font-size: 8px; font-family: Arial, sans-serif;">
                                <thead>
                                    <tr style="background: #fff;">
                                        <th rowspan="2" style="border: 1px solid #000; width: 20px; font-weight: 900;">Kode<br>Guru</th>
                                        <th rowspan="2" style="border: 1px solid #000; width: 140px; text-align: left; padding-left: 4px; font-weight: 900;">NAMA GURU</th>
                                        <th rowspan="2" style="border: 1px solid #000; text-align: left; padding-left: 4px; font-weight: 900;">Mata Pelajaran</th>
                                        <th colspan="5" style="border: 1px solid #000; font-weight: 900;">Hari Mengajar</th>
                                        <th rowspan="2" style="border: 1px solid #000; width: 20px; background: #86efac; font-weight: 900;">JML<br>Jam</th>
                                    </tr>
                                    <tr style="background: #fff;">
                                        <th style="border: 1px solid #000; width: 11px;">S</th>
                                        <th style="border: 1px solid #000; width: 11px;">S</th>
                                        <th style="border: 1px solid #000; width: 11px;">R</th>
                                        <th style="border: 1px solid #000; width: 11px;">K</th>
                                        <th style="border: 1px solid #000; width: 11px;">J</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($officialTeachersList as $code => $t)
                                        <tr>
                                            <td style="border: 1px solid #000; text-align: center; font-weight: bold;">{{ $code }}</td>
                                            <td style="border: 1px solid #000; text-align: left; padding-left: 3px; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 140px;">
                                                {{ $t['name'] }}
                                            </td>
                                            <td style="border: 1px solid #000; text-align: left; padding-left: 3px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                {{ $t['subject'] }}
                                            </td>
                                            <td style="border: 1px solid #000; text-align: center;">{{ $t['days'][0] }}</td>
                                            <td style="border: 1px solid #000; text-align: center;">{{ $t['days'][1] }}</td>
                                            <td style="border: 1px solid #000; text-align: center;">{{ $t['days'][2] }}</td>
                                            <td style="border: 1px solid #000; text-align: center;">{{ $t['days'][3] }}</td>
                                            <td style="border: 1px solid #000; text-align: center;">{{ $t['days'][4] }}</td>
                                            <td style="border: 1px solid #000; text-align: center; background: #86efac; font-weight: 900;">{{ $t['total'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <!-- TABLE 2: WALI KELAS -->
                            <table style="width: 100%; border-collapse: collapse; border: 1px solid #000; font-size: 8px; font-family: Arial, sans-serif; margin-top: 6px;">
                                <thead>
                                    <tr style="background: #fed7aa;">
                                        <th style="border: 1px solid #000; width: 24px; font-weight: 900;">No</th>
                                        <th style="border: 1px solid #000; text-align: left; padding-left: 6px; font-weight: 900;">NAMA GURU</th>
                                        <th style="border: 1px solid #000; width: 80px; font-weight: 900;">WALI KELAS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($officialHomeroomTeachers as $idx => $wk)
                                        <tr>
                                            <td style="border: 1px solid #000; text-align: center; font-weight: bold;">{{ $idx }}</td>
                                            <td style="border: 1px solid #000; text-align: left; padding-left: 6px; font-weight: 600;">{{ $wk['name'] }}</td>
                                            <td style="border: 1px solid #000; text-align: center; font-weight: 800;">{{ $wk['class'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <!-- SIGNATURES BLOCK -->
                            <div style="margin-top: 10px; display: flex; justify-content: space-between; font-size: 8.5px; padding: 0 8px; text-align: center;">
                                <div style="width: 48%;">
                                    <div>Mengetahui,</div>
                                    <div style="font-weight: bold;">{{ $officialHeader['principal_title'] ?? 'Kepala MAN 20 Jakarta' }}</div>
                                    <div style="height: 48px; display: flex; align-items: center; justify-content: center; position: relative; margin: 3px auto;">
                                        @if(!empty($officialHeader['school_stamp']) && file_exists(public_path($officialHeader['school_stamp'])))
                                            <img src="{{ asset($officialHeader['school_stamp']) }}" alt="Stempel" style="max-height: 45px; position: absolute; left: 10px; opacity: 0.85; mix-blend-mode: multiply;">
                                        @endif
                                        @if(!empty($officialHeader['principal_signature']) && file_exists(public_path($officialHeader['principal_signature'])))
                                            <img src="{{ asset($officialHeader['principal_signature']) }}" alt="Tanda Tangan Kepala" style="max-height: 46px; object-fit: contain; position: relative; z-index: 2;">
                                        @endif
                                    </div>
                                    <div><strong><u>{{ $officialHeader['principal_name'] ?? 'Drs. Sodikin, M.Si' }}</u></strong></div>
                                    <div>NIP. {{ $officialHeader['principal_nip'] ?? '196810212003121001' }}</div>
                                </div>

                                <div style="width: 48%;">
                                    <div>{{ $officialHeader['date_signed'] ?? 'Jakarta, 13 Juli 2026' }}</div>
                                    <div style="font-weight: bold;">{{ $officialHeader['academic_head_title'] ?? 'Wakil Kepala Bidang Akademik' }}</div>
                                    <div style="height: 48px; display: flex; align-items: center; justify-content: center; margin: 3px auto;">
                                        @if(!empty($officialHeader['academic_head_signature']) && file_exists(public_path($officialHeader['academic_head_signature'])))
                                            <img src="{{ asset($officialHeader['academic_head_signature']) }}" alt="Tanda Tangan Waka" style="max-height: 46px; object-fit: contain;">
                                        @endif
                                    </div>
                                    <div><strong><u>{{ $officialHeader['academic_head_name'] ?? 'Temy Yulianti, S.Pd., M.Pd' }}</u></strong></div>
                                    <div>NIP. {{ $officialHeader['academic_head_nip'] ?? '197712102009122001' }}</div>
                                </div>
                            </div>

                        </div>

                    </div>

                </div>
            </div>

        </div>

    @else

        <!-- INTERACTIVE MATRIX TABLE (DAILY) -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto w-full">
                <table class="w-full text-xs border-collapse min-w-[950px]">
                    <thead>
                        <tr class="bg-slate-900 text-white text-center">
                            <th class="py-3 px-3 w-28 border-r border-slate-800">Kelas / Rombel</th>
                            @foreach($periods as $p)
                                <th class="py-3 px-2 border-r border-slate-800 last:border-r-0 min-w-[130px]">
                                    <div class="font-bold">Jam {{ $p }}</div>
                                    <div class="text-[10px] text-slate-400 font-normal">
                                        @php
                                            $times = [
                                                1 => '06:30-07:15', 2 => '07:15-08:00', 3 => '08:00-08:45',
                                                4 => '08:45-09:30', 5 => '09:50-10:35', 6 => '10:35-11:20',
                                                7 => '11:20-12:05', 8 => '12:45-13:30', 9 => '13:30-14:15',
                                                10 => '14:15-15:00'
                                            ];
                                        @endphp
                                        {{ $times[$p] ?? '' }}
                                    </div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach($classrooms as $cls)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-3 px-3 text-center font-bold text-slate-800 bg-slate-50 border-r border-slate-200">
                                    <div class="font-extrabold text-sm">Kelas {{ $cls->name }}</div>
                                    <div class="text-[10px] text-slate-400">Tingkat {{ $cls->grade }}</div>
                                </td>

                                @foreach($periods as $p)
                                    @php
                                        $item = $masterGrid[$cls->id][$p] ?? null;
                                    @endphp
                                    <td class="p-1.5 border-r border-slate-200 last:border-r-0 align-top">
                                        @if($item)
                                            <div class="rounded-xl p-2 border shadow-2xs"
                                                 style="background-color: {{ $item->subject->color ? $item->subject->color . '15' : '#eff6ff' }}; border-color: {{ $item->subject->color ? $item->subject->color . '40' : '#bfdbfe' }};">
                                                <div class="font-bold text-[10px] leading-tight truncate" style="color: {{ $item->subject->color ?: '#1e40af' }}">
                                                    {{ $item->subject->name }}
                                                </div>
                                                <div class="text-[9px] text-slate-600 mt-0.5 truncate font-medium">
                                                    {{ $item->teacher->name }}
                                                </div>
                                            </div>
                                        @else
                                            <div class="h-12 rounded-lg bg-slate-50/50 border border-dashed border-slate-200 flex items-center justify-center text-[10px] text-slate-300">
                                                -
                                            </div>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    @endif

</div>
@endsection
