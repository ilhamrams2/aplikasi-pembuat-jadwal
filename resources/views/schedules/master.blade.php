@extends('layouts.app')

@section('title', 'Master Matrix Jadwal - SIKAD MAN 20 Jakarta')

@section('content')
<div class="space-y-6">

    <!-- TOOLBAR -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-800 tracking-tight">Master Matrix Jadwal Madrasah</h2>
            <p class="text-xs text-slate-500 mt-0.5">Tampilan matriks jadwal 15 rombel kelas per hari</p>
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

            <!-- Print Master Schedule -->
            <a href="{{ route('schedules.print.all') }}" target="_blank" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition border border-slate-200">
                <i class="fa-solid fa-print mr-1"></i> Cetak Master
            </a>
        </div>
    </div>

    <!-- DAY SELECTOR TABS -->
    <div class="flex items-center gap-2 overflow-x-auto pb-1">
        @foreach($days as $day)
            <a href="{{ route('schedules.index', ['type' => 'master', 'day' => $day]) }}" 
               class="px-5 py-2.5 rounded-xl font-bold text-xs transition whitespace-nowrap {{ $selectedDay === $day ? 'bg-blue-600 text-white shadow-md shadow-blue-500/20' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200' }}">
                <i class="fa-regular fa-calendar-check mr-1.5"></i> Hari {{ $day }}
            </a>
        @endforeach
    </div>

    <!-- MASTER MATRIX TABLE -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-900 text-white text-center">
                        <th class="py-3 px-3 w-24 border-r border-slate-800">Kelas / Rombel</th>
                        @foreach($periods as $p)
                            <th class="py-3 px-2 border-r border-slate-800 last:border-r-0 min-w-[130px]">
                                <div class="font-bold">Jam {{ $p }}</div>
                                <div class="text-[10px] text-slate-400 font-normal">
                                    @php
                                        $times = [
                                            1 => '06:45-07:30', 2 => '07:30-08:15', 3 => '08:15-09:00',
                                            4 => '09:00-09:45', 5 => '10:15-11:00', 6 => '11:00-11:45',
                                            7 => '11:45-12:30', 8 => '13:00-13:45', 9 => '13:45-14:30',
                                            10 => '14:30-15:15'
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
                            <!-- Classroom Name -->
                            <td class="py-3 px-3 text-center font-bold text-slate-800 bg-slate-50 border-r border-slate-200">
                                <div class="font-extrabold text-sm">Kelas {{ $cls->name }}</div>
                                <div class="text-[10px] text-slate-400">Tingkat {{ $cls->grade }}</div>
                            </td>

                            <!-- Periods -->
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

</div>
@endsection
