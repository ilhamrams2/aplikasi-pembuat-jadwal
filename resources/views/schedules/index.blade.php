@extends('layouts.app')

@section('title', 'Jadwal Pelajaran - SIKAD MAN 20 Jakarta')

@section('content')
<div class="space-y-6">

    <!-- TOOLBAR & HEADER -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-800 tracking-tight">Manajemen Jadwal Pelajaran</h2>
            <p class="text-xs text-slate-500 mt-0.5">Lihat, atur, dan sesuaikan jadwal per kelas atau per guru</p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <!-- View Mode Switcher -->
            <div class="inline-flex p-1 bg-slate-100 rounded-xl border border-slate-200 text-xs font-semibold">
                <a href="{{ route('schedules.index', ['type' => 'class', 'classroom_id' => $selectedClassId]) }}" 
                   class="px-3 py-1.5 rounded-lg transition {{ $viewType === 'class' ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                   <i class="fa-solid fa-users mr-1"></i> Per Kelas
                </a>
                <a href="{{ route('schedules.index', ['type' => 'teacher', 'teacher_id' => $selectedTeacherId]) }}" 
                   class="px-3 py-1.5 rounded-lg transition {{ $viewType === 'teacher' ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                   <i class="fa-solid fa-user-tie mr-1"></i> Per Guru
                </a>
                <a href="{{ route('schedules.index', ['type' => 'master']) }}" 
                   class="px-3 py-1.5 rounded-lg transition {{ $viewType === 'master' ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                   <i class="fa-solid fa-table mr-1"></i> Master Matrix
                </a>
            </div>

            <!-- Print Button -->
            @if($viewType === 'class' && $selectedEntity)
                <a href="{{ route('schedules.print.class', $selectedEntity->id) }}" target="_blank" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition border border-slate-200">
                    <i class="fa-solid fa-print mr-1"></i> Cetak Kelas
                </a>
            @elseif($viewType === 'teacher' && $selectedEntity)
                <a href="{{ route('schedules.print.teacher', $selectedEntity->id) }}" target="_blank" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition border border-slate-200">
                    <i class="fa-solid fa-print mr-1"></i> Cetak Guru
                </a>
            @endif

            <!-- Clear Schedules -->
            <form action="{{ route('schedules.clear') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin MENGOSONGKAN seluruh jadwal? Tindakan ini tidak dapat dibatalkan.');">
                @csrf
                <button type="submit" class="px-3 py-2 bg-rose-50 hover:bg-rose-100 text-rose-600 text-xs font-bold rounded-xl transition border border-rose-200">
                    <i class="fa-solid fa-trash-can mr-1"></i> Reset
                </button>
            </form>
        </div>
    </div>

    <!-- FILTER SELECTOR CARD -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
        @if($viewType === 'class')
            <form method="GET" action="{{ route('schedules.index') }}" class="flex items-center gap-3 w-full md:w-auto">
                <input type="hidden" name="type" value="class">
                <label class="text-xs font-bold text-slate-600 flex-shrink-0">Pilih Kelas / Rombel:</label>
                <select name="classroom_id" onchange="this.form.submit()" class="text-xs font-semibold px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                    @foreach($classrooms as $cls)
                        <option value="{{ $cls->id }}" {{ $selectedClassId == $cls->id ? 'selected' : '' }}>
                            Kelas {{ $cls->name }} (Tingkat {{ $cls->grade }})
                        </option>
                    @endforeach
                </select>
            </form>

            @if($selectedEntity)
                <div class="text-xs text-slate-600 flex items-center gap-4">
                    <span>Wali Kelas: <strong>{{ $selectedEntity->homeroomTeacher->name ?? 'Belum ditentukan' }}</strong></span>
                    <span>Total Jam Terjadwal: <strong class="text-blue-600 font-extrabold">{{ count($schedulesGrid ? array_merge(...array_values($schedulesGrid)) : []) }} JP</strong></span>
                </div>
            @endif

        @elseif($viewType === 'teacher')
            <form method="GET" action="{{ route('schedules.index') }}" class="flex items-center gap-3 w-full md:w-auto">
                <input type="hidden" name="type" value="teacher">
                <label class="text-xs font-bold text-slate-600 flex-shrink-0">Pilih Guru:</label>
                <select name="teacher_id" onchange="this.form.submit()" class="text-xs font-semibold px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none max-w-xs">
                    @foreach($teachers as $t)
                        <option value="{{ $t->id }}" {{ $selectedTeacherId == $t->id ? 'selected' : '' }}>
                            {{ $t->order_no ? '[' . $t->order_no . '] ' : '' }}{{ $t->name }}
                        </option>
                    @endforeach
                </select>
            </form>

            @if($selectedEntity)
                <div class="text-xs text-slate-600 flex items-center gap-4">
                    <span>Tugas Tambahan: <strong>{{ $selectedEntity->additional_role ?: 'Tidak ada' }}</strong></span>
                    <span>Total Mengajar: <strong class="text-blue-600 font-extrabold">{{ count($schedulesGrid ? array_merge(...array_values($schedulesGrid)) : []) }} JP</strong></span>
                </div>
            @endif
        @endif
    </div>

    <!-- TIMETABLE GRID TABLE -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-900 text-white text-center">
                        <th class="py-3 px-2 w-16 border-r border-slate-800">Hari</th>
                        @foreach($periods as $p)
                            <th class="py-3 px-2 border-r border-slate-800 last:border-r-0 min-w-[120px]">
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
                    @foreach($days as $day)
                        <tr class="hover:bg-slate-50/50 transition">
                            <!-- Hari Header -->
                            <td class="py-4 px-2 text-center font-bold text-slate-700 bg-slate-50 border-r border-slate-200">
                                <span class="px-2 py-1 rounded-md bg-white border border-slate-200 shadow-2xs">{{ $day }}</span>
                            </td>

                            <!-- Period Cells -->
                            @foreach($periods as $p)
                                @php
                                    $item = $schedulesGrid[$day][$p] ?? null;
                                @endphp
                                <td class="p-1.5 border-r border-slate-200 last:border-r-0 align-top">
                                    @if($item)
                                        <div class="group relative rounded-xl p-2.5 border shadow-2xs transition-all hover:shadow-md"
                                             style="background-color: {{ $item->subject->color ? $item->subject->color . '15' : '#eff6ff' }}; border-color: {{ $item->subject->color ? $item->subject->color . '40' : '#bfdbfe' }};">
                                            
                                            <!-- Subject Code/Name -->
                                            <div class="font-extrabold text-[11px] leading-tight" style="color: {{ $item->subject->color ?: '#1e40af' }}">
                                                {{ $item->subject->name }}
                                            </div>

                                            <!-- Teacher or Class info depending on view mode -->
                                            <div class="text-[10px] text-slate-600 mt-1 font-medium leading-tight truncate">
                                                @if($viewType === 'class')
                                                    <i class="fa-solid fa-user-tie text-[9px] text-slate-400 mr-0.5"></i>
                                                    {{ $item->teacher->name }}
                                                @else
                                                    <i class="fa-solid fa-chalkboard text-[9px] text-slate-400 mr-0.5"></i>
                                                    Kelas {{ $item->classroom->name }}
                                                @endif
                                            </div>

                                            <div class="mt-1 flex items-center justify-between text-[9px] text-slate-400 font-mono">
                                                <span>{{ $item->room ?: 'R.' . $item->classroom->name }}</span>
                                                <!-- Delete button -->
                                                <form action="{{ route('schedules.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus jam pelajaran ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="opacity-0 group-hover:opacity-100 text-rose-500 hover:text-rose-700 transition" title="Hapus Slot">
                                                        <i class="fa-solid fa-trash-can"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @else
                                        <!-- Empty Slot -->
                                        @if($viewType === 'class')
                                            <button onclick="openAssignModal('{{ $day }}', {{ $p }})"
                                                    class="w-full h-16 rounded-xl border border-dashed border-slate-200 hover:border-blue-400 hover:bg-blue-50/50 flex flex-col items-center justify-center text-slate-300 hover:text-blue-600 transition group">
                                                <i class="fa-solid fa-plus text-xs group-hover:scale-125 transition-transform"></i>
                                                <span class="text-[9px] mt-0.5">Isi</span>
                                            </button>
                                        @else
                                            <div class="w-full h-16 rounded-xl bg-slate-50/60 border border-slate-100 flex items-center justify-center text-[10px] text-slate-300">
                                                -
                                            </div>
                                        @endif
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

<!-- SEARCHABLE MANUAL ASSIGNMENT MODAL (For Class View) -->
@if($viewType === 'class' && $selectedClassId)
<div id="assignModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center z-50 hidden p-4 transition-opacity">
    <div class="bg-white rounded-3xl shadow-2xl max-w-2xl w-full p-6 border border-slate-100 flex flex-col max-h-[92vh] overflow-hidden">
        
        <!-- Header -->
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 flex-shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white flex items-center justify-center text-lg shadow-md shadow-blue-500/20">
                    <i class="fa-solid fa-calendar-plus"></i>
                </div>
                <div>
                    <h3 class="font-extrabold text-base text-slate-800 tracking-tight">Isi Jadwal Pelajaran</h3>
                    <p class="text-xs text-slate-500">Pilih mata pelajaran & guru pengampu untuk Kelas {{ $selectedEntity->name ?? '' }}</p>
                </div>
            </div>
            <button onclick="closeAssignModal()" class="w-8 h-8 rounded-full bg-slate-100 text-slate-400 hover:text-slate-700 hover:bg-slate-200 flex items-center justify-center transition">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <form id="assignForm" action="{{ route('schedules.store') }}" method="POST" onsubmit="return validateAssignForm()" class="mt-4 flex-1 flex flex-col overflow-hidden space-y-3.5">
            @csrf
            <input type="hidden" name="classroom_id" value="{{ $selectedClassId }}">
            <input type="hidden" id="modalDay" name="day" value="">
            <input type="hidden" id="modalPeriod" name="period" value="">
            <input type="hidden" id="selectedAssignmentInput" name="teacher_assignment_id" value="">

            <!-- Time Slot Info Pill -->
            <div class="p-3 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-100 rounded-2xl text-xs text-blue-900 flex items-center justify-between flex-shrink-0">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-clock text-blue-600"></i>
                    <span>Waktu Slot: <strong id="modalSlotLabel" class="text-blue-950 font-bold">-</strong></span>
                </div>
                <span class="px-2.5 py-0.5 rounded-full bg-blue-200/70 text-blue-900 text-[11px] font-bold">
                    Kelas {{ $selectedEntity->name ?? '' }}
                </span>
            </div>

            <!-- Live Search Bar & Category Filter Pills -->
            <div class="flex-shrink-0 space-y-2">
                <div class="relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" id="asgSearchInput" oninput="filterAssignments()" placeholder="Ketik untuk mencari mata pelajaran atau guru (contoh: Biologi, Deni, Kimia, Arab)..."
                           class="w-full pl-9 pr-8 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white outline-none transition">
                    <button type="button" id="clearSearchBtn" onclick="clearSearch()" class="hidden absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs p-1">
                        <i class="fa-solid fa-circle-xmark"></i>
                    </button>
                </div>

                <div class="flex items-center gap-1.5 overflow-x-auto pb-0.5 text-[11px]">
                    <span class="text-slate-400 font-semibold mr-1">Filter:</span>
                    <button type="button" onclick="setCategoryFilter('all')" class="cat-pill active px-2.5 py-1 rounded-lg font-semibold bg-blue-600 text-white transition" data-cat="all">
                        Semua ({{ count($availableAssignments) }})
                    </button>
                    @php
                        $categoriesInClass = $availableAssignments->pluck('subject.category')->unique()->filter();
                    @endphp
                    @foreach($categoriesInClass as $cCat)
                        <button type="button" onclick="setCategoryFilter('{{ $cCat }}')" class="cat-pill px-2.5 py-1 rounded-lg font-semibold bg-slate-100 text-slate-600 hover:bg-slate-200 transition" data-cat="{{ $cCat }}">
                            {{ $cCat }}
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Validation Error Banner (hidden by default) -->
            <div id="validationAlert" class="hidden p-3 bg-rose-50 border border-rose-200 rounded-xl text-rose-700 text-xs flex items-center gap-2 flex-shrink-0">
                <i class="fa-solid fa-circle-exclamation text-rose-500 text-sm"></i>
                <span id="validationAlertText">Silakan klik salah satu mata pelajaran di bawah untuk memilih.</span>
            </div>

            <!-- Scrollable Assignment Cards List -->
            <div id="assignmentListContainer" class="flex-1 overflow-y-auto pr-1 space-y-2 min-h-[200px] max-h-[300px]">
                @foreach($availableAssignments as $asg)
                    @php
                        $color = $asg->subject->color ?: '#2563eb';
                        $isFull = $asg->schedules_count >= $asg->hours_per_week;
                    @endphp
                    <div class="asg-card group relative p-3 rounded-2xl border border-slate-200 bg-white hover:border-blue-300 hover:shadow-xs cursor-pointer transition flex items-center justify-between gap-3"
                         data-id="{{ $asg->id }}"
                         data-teacher-id="{{ $asg->teacher_id }}"
                         data-name="{{ strtolower($asg->subject->name) }}"
                         data-teacher="{{ strtolower($asg->teacher->name) }}"
                         data-cat="{{ $asg->subject->category }}"
                         data-color="{{ $color }}"
                         data-sub-name="{{ $asg->subject->name }}"
                         data-teach-name="{{ $asg->teacher->name }}"
                         onclick="selectCard(this)">
                        
                        <div class="flex items-center gap-3 min-w-0">
                            <!-- Color bar / circle -->
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white font-extrabold text-xs flex-shrink-0 shadow-xs"
                                 style="background-color: {{ $color }};">
                                {{ substr($asg->subject->code ?? $asg->subject->name, 0, 3) }}
                            </div>

                            <!-- Subject & Teacher Info -->
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <h4 class="font-extrabold text-xs text-slate-800 truncate leading-tight">{{ $asg->subject->name }}</h4>
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-slate-100 text-slate-600 flex-shrink-0">
                                        {{ $asg->subject->category }}
                                    </span>
                                </div>
                                <div class="text-[11px] text-slate-500 mt-0.5 flex items-center gap-1.5 truncate">
                                    <i class="fa-solid fa-user-tie text-[10px] text-slate-400"></i>
                                    <span class="font-medium text-slate-700 truncate">{{ $asg->teacher->name }}</span>
                                    @if($asg->teacher->additional_role)
                                        <span class="text-[10px] text-slate-400">({{ Str::limit($asg->teacher->additional_role, 20) }})</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Status Badges & Radio Indicator -->
                        <div class="flex items-center gap-3 flex-shrink-0">
                            <!-- Hours Progress Badge -->
                            <div class="text-right">
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold {{ $isFull ? 'bg-slate-100 text-slate-600' : 'bg-blue-50 text-blue-700' }}">
                                    {{ $asg->schedules_count }} / {{ $asg->hours_per_week }} JP
                                </span>
                                <!-- Clash indicator dynamically set by JS -->
                                <div class="clash-indicator mt-0.5 text-[9px] font-bold text-emerald-600 text-right">
                                    <i class="fa-solid fa-check text-[8px]"></i> Tersedia
                                </div>
                            </div>

                            <!-- Radio Circle Indicator -->
                            <div class="radio-indicator w-5 h-5 rounded-full border-2 border-slate-300 group-hover:border-blue-400 flex items-center justify-center transition">
                                <div class="radio-dot w-2.5 h-2.5 rounded-full bg-blue-600 hidden"></div>
                            </div>
                        </div>
                    </div>
                @endforeach

                <!-- Empty Search Result -->
                <div id="noMatchMessage" class="hidden p-8 text-center text-slate-400">
                    <i class="fa-solid fa-magnifying-glass text-2xl mb-2 text-slate-300 block"></i>
                    <p class="text-xs font-semibold text-slate-600">Tidak ada mata pelajaran atau guru yang cocok</p>
                    <p class="text-[11px] text-slate-400 mt-0.5">Coba gunakan kata kunci pencarian lain atau pilih kategori 'Semua'</p>
                </div>
            </div>

            <!-- Footer Selected Bar & Actions -->
            <div class="pt-3 border-t border-slate-100 flex items-center justify-between flex-shrink-0">
                <div class="text-xs truncate mr-2">
                    <span class="text-slate-400">Terpilih:</span>
                    <strong id="selectedLabel" class="text-slate-800 ml-1 font-bold">Belum ada pilihan</strong>
                </div>

                <div class="flex items-center gap-2 flex-shrink-0">
                    <button type="button" onclick="closeAssignModal()" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition">
                        Batal
                    </button>
                    <button type="submit" id="submitAssignBtn" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-500/20 transition transform active:scale-95 flex items-center gap-1.5">
                        <i class="fa-solid fa-check"></i>
                        <span>Simpan ke Jadwal</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    // Map of busy teachers: key = "teacherId_day_period", value = classroom name
    const teacherBusyMap = {!! json_encode($teacherBusyMap ?? []) !!};
    let currentCategoryFilter = 'all';

    function openAssignModal(day, period) {
        document.getElementById('modalDay').value = day;
        document.getElementById('modalPeriod').value = period;

        const periodTimes = {
            1: '06:45-07:30', 2: '07:30-08:15', 3: '08:15-09:00',
            4: '09:00-09:45', 5: '10:15-11:00', 6: '11:00-11:45',
            7: '11:45-12:30', 8: '13:00-13:45', 9: '13:45-14:30',
            10: '14:30-15:15'
        };

        const timeStr = periodTimes[period] ? ' (' + periodTimes[period] + ')' : '';
        document.getElementById('modalSlotLabel').innerText = day + ' • Jam ke-' + period + timeStr;
        
        // Reset selection & search
        document.getElementById('selectedAssignmentInput').value = '';
        document.getElementById('selectedLabel').innerHTML = '<span class="text-slate-400 font-normal">Klik salah satu mapel di atas</span>';
        document.getElementById('validationAlert').classList.add('hidden');
        document.getElementById('asgSearchInput').value = '';
        document.getElementById('clearSearchBtn').classList.add('hidden');
        
        // Reset filter
        setCategoryFilter('all');

        // Check availability for each card at this specific day & period
        const cards = document.querySelectorAll('.asg-card');
        cards.forEach(card => {
            // Remove previous selected styling
            card.classList.remove('ring-2', 'ring-blue-600', 'border-blue-500', 'bg-blue-50/50');
            card.querySelector('.radio-indicator').classList.remove('border-blue-600');
            card.querySelector('.radio-dot').classList.add('hidden');

            const tId = card.dataset.teacherId;
            const clashKey = tId + '_' + day + '_' + period;
            const clashBadge = card.querySelector('.clash-indicator');

            if (teacherBusyMap[clashKey]) {
                const busyClass = teacherBusyMap[clashKey];
                clashBadge.className = 'clash-indicator mt-0.5 text-[9px] font-bold text-rose-600 text-right';
                clashBadge.innerHTML = '<i class="fa-solid fa-triangle-exclamation text-[8px]"></i> Bentrok (' + busyClass + ')';
                card.dataset.hasClash = 'true';
                card.dataset.clashClass = busyClass;
            } else {
                clashBadge.className = 'clash-indicator mt-0.5 text-[9px] font-bold text-emerald-600 text-right';
                clashBadge.innerHTML = '<i class="fa-solid fa-check text-[8px]"></i> Tersedia';
                card.dataset.hasClash = 'false';
                card.dataset.clashClass = '';
            }
        });

        document.getElementById('assignModal').classList.remove('hidden');
        setTimeout(() => document.getElementById('asgSearchInput').focus(), 100);
    }

    function closeAssignModal() {
        document.getElementById('assignModal').classList.add('hidden');
    }

    function selectCard(card) {
        // Deselect all
        document.querySelectorAll('.asg-card').forEach(c => {
            c.classList.remove('ring-2', 'ring-blue-600', 'border-blue-500', 'bg-blue-50/50');
            c.querySelector('.radio-indicator').classList.remove('border-blue-600');
            c.querySelector('.radio-dot').classList.add('hidden');
        });

        // Select clicked card
        card.classList.add('ring-2', 'ring-blue-600', 'border-blue-500', 'bg-blue-50/50');
        card.querySelector('.radio-indicator').classList.add('border-blue-600');
        card.querySelector('.radio-dot').classList.remove('hidden');

        const asgId = card.dataset.id;
        document.getElementById('selectedAssignmentInput').value = asgId;

        // Update selected display
        const subName = card.dataset.subName;
        const teachName = card.dataset.teachName;
        const hasClash = card.dataset.hasClash === 'true';
        const clashClass = card.dataset.clashClass;

        if (hasClash) {
            document.getElementById('selectedLabel').innerHTML = 
                '<span class="text-blue-700 font-extrabold">' + subName + '</span> &bull; ' + teachName + 
                ' <span class="ml-1 text-rose-600 text-[10px] font-bold">(Peringatan: Guru sedang di ' + clashClass + ')</span>';
        } else {
            document.getElementById('selectedLabel').innerHTML = 
                '<span class="text-blue-700 font-extrabold">' + subName + '</span> &bull; <span class="text-slate-600 font-medium">' + teachName + '</span>';
        }

        document.getElementById('validationAlert').classList.add('hidden');
    }

    function filterAssignments() {
        const query = document.getElementById('asgSearchInput').value.trim().toLowerCase();
        const clearBtn = document.getElementById('clearSearchBtn');
        
        if (query) {
            clearBtn.classList.remove('hidden');
        } else {
            clearBtn.classList.add('hidden');
        }

        let visibleCount = 0;
        const cards = document.querySelectorAll('.asg-card');

        cards.forEach(card => {
            const name = card.dataset.name;
            const teacher = card.dataset.teacher;
            const cat = card.dataset.cat;

            const matchQuery = !query || name.includes(query) || teacher.includes(query);
            const matchCategory = currentCategoryFilter === 'all' || cat === currentCategoryFilter;

            if (matchQuery && matchCategory) {
                card.classList.remove('hidden');
                visibleCount++;
            } else {
                card.classList.add('hidden');
            }
        });

        const noMatch = document.getElementById('noMatchMessage');
        if (visibleCount === 0) {
            noMatch.classList.remove('hidden');
        } else {
            noMatch.classList.add('hidden');
        }
    }

    function clearSearch() {
        document.getElementById('asgSearchInput').value = '';
        document.getElementById('clearSearchBtn').classList.add('hidden');
        filterAssignments();
        document.getElementById('asgSearchInput').focus();
    }

    function setCategoryFilter(category) {
        currentCategoryFilter = category;
        document.querySelectorAll('.cat-pill').forEach(btn => {
            if (btn.dataset.cat === category) {
                btn.className = 'cat-pill active px-2.5 py-1 rounded-lg font-semibold bg-blue-600 text-white transition';
            } else {
                btn.className = 'cat-pill px-2.5 py-1 rounded-lg font-semibold bg-slate-100 text-slate-600 hover:bg-slate-200 transition';
            }
        });
        filterAssignments();
    }

    function validateAssignForm() {
        const selectedId = document.getElementById('selectedAssignmentInput').value;
        if (!selectedId) {
            const alertBox = document.getElementById('validationAlert');
            alertBox.classList.remove('hidden');
            document.getElementById('validationAlertText').innerText = 'Silakan klik salah satu mata pelajaran di daftar sebelum menyimpan.';
            alertBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            return false;
        }

        // Check if chosen teacher has clash
        const selectedCard = document.querySelector(`.asg-card[data-id="${selectedId}"]`);
        if (selectedCard && selectedCard.dataset.hasClash === 'true') {
            const clashClass = selectedCard.dataset.clashClass;
            return confirm(`Peringatan: Guru ini sudah memiliki jadwal mengajar di ${clashClass} pada jam ini. Apakah Anda yakin tetap ingin memasukkan jadwal ini?`);
        }

        return true;
    }
</script>
@endif
@endsection
