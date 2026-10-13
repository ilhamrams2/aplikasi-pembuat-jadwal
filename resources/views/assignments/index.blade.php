@extends('layouts.app')

@section('title', 'Distribusi Tugas Guru - SIKAD MAN 20 Jakarta')

@section('content')
<div class="space-y-6">

    <!-- HEADER & ACTIONS -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-800 tracking-tight">Distribusi Pembagian Tugas Mengajar</h2>
            <p class="text-xs text-slate-500 mt-0.5">Pemetaan pembagian jam guru ke 15 rombel kelas (seperti dokumen Excel)</p>
        </div>

        <button onclick="openDistributionModal()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-500/20 transition transform active:scale-95">
            <i class="fa-solid fa-plus text-xs"></i>
            <span>+ Tambah Pembagian Tugas</span>
        </button>
    </div>

    <!-- EXCEL-STYLE DISTRIBUTION MATRIX -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2 bg-slate-50">
            <h3 class="font-extrabold text-xs uppercase tracking-wider text-slate-700">
                <i class="fa-solid fa-table-list mr-1.5 text-blue-600"></i> Matriks Distribusi Guru & Kelas (15 Rombel)
            </h3>
            <span class="text-xs text-slate-500">Angka dalam kolom menunjukkan alokasi JP per minggu &bull; Klik tombol <strong>Atur</strong> untuk input cepat</span>
        </div>

        <div class="overflow-x-auto w-full">
            <table class="w-full text-[11px] text-center border-collapse min-w-[1150px]">
                <thead>
                    <tr class="bg-slate-900 text-white text-[10px] uppercase font-bold">
                        <th class="py-2.5 px-2 border-r border-slate-800 w-10">No</th>
                        <th class="py-2.5 px-3 border-r border-slate-800 text-left min-w-[200px]">Nama Guru</th>
                        <th class="py-2.5 px-2 border-r border-slate-800 w-16">Target</th>
                        
                        <!-- Kelas X (1-5) -->
                        @foreach(['X.1', 'X.2', 'X.3', 'X.4', 'X.5'] as $cName)
                            <th class="py-2.5 px-1 border-r border-slate-800 bg-blue-950/80 w-11">{{ $cName }}</th>
                        @endforeach

                        <!-- Kelas XI (1-5) -->
                        @foreach(['XI.1', 'XI.2', 'XI.3', 'XI.4', 'XI.5'] as $cName)
                            <th class="py-2.5 px-1 border-r border-slate-800 bg-indigo-950/80 w-11">{{ $cName }}</th>
                        @endforeach

                        <!-- Kelas XII (1-5) -->
                        @foreach(['XII.1', 'XII.2', 'XII.3', 'XII.4', 'XII.5'] as $cName)
                            <th class="py-2.5 px-1 border-r border-slate-800 bg-purple-950/80 w-11">{{ $cName }}</th>
                        @endforeach

                        <th class="py-2.5 px-2 w-14 bg-emerald-950 border-r border-slate-800">Total</th>
                        <th class="py-2.5 px-3 w-28 bg-slate-900 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @php $classroomLookup = $classrooms->keyBy('name'); @endphp
                    @foreach($teachers as $t)
                        @php
                            $rowTotal = 0;
                        @endphp
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-2 px-1 text-slate-400 font-bold border-r border-slate-200">{{ $t->order_no ?? $loop->iteration }}</td>
                            <td class="py-2 px-3 text-left font-bold text-slate-800 border-r border-slate-200 truncate">
                                <div>{{ $t->name }}</div>
                                <div class="text-[9px] text-slate-400 font-normal">{{ $t->additional_role ?: '-' }}</div>
                            </td>
                            <td class="py-2 px-1 text-slate-500 font-mono border-r border-slate-200">{{ $t->target_hours }}</td>

                            <!-- 15 Classes -->
                            @foreach(['X.1', 'X.2', 'X.3', 'X.4', 'X.5', 'XI.1', 'XI.2', 'XI.3', 'XI.4', 'XI.5', 'XII.1', 'XII.2', 'XII.3', 'XII.4', 'XII.5'] as $cName)
                                @php
                                    $cObj = $classroomLookup[$cName] ?? null;
                                    $items = ($cObj && isset($matrix[$t->id][$cObj->id])) ? $matrix[$t->id][$cObj->id] : [];
                                    $classHours = array_sum(array_column($items, 'hours'));
                                    $rowTotal += $classHours;
                                @endphp
                                <td class="py-1 px-1 border-r border-slate-200 font-bold {{ $classHours > 0 ? 'bg-blue-50/60 text-blue-700' : 'text-slate-200' }}">
                                    @if($classHours > 0)
                                        <span title="{{ implode(', ', array_column($items, 'subject')) }} ({{ $classHours }} JP)">
                                            {{ $classHours }}
                                        </span>
                                    @else
                                        -
                                    @endif
                                </td>
                            @endforeach

                            <td class="py-2 px-1 font-extrabold text-xs border-r border-slate-200 {{ $rowTotal >= 24 ? 'text-emerald-700 bg-emerald-50/50' : 'text-amber-600 bg-amber-50/50' }}">
                                {{ $rowTotal }}
                            </td>

                            <!-- ACTION COLUMN -->
                            <td class="py-2 px-2 text-center whitespace-nowrap">
                                <button type="button" 
                                        onclick="openDistributionModal({{ $t->id }}, '{{ addslashes($t->name) }}', {{ $t->target_hours }})"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-sm transition active:scale-95"
                                        title="Atur Distribusi Mengajar {{ $t->name }}">
                                    <i class="fa-solid fa-pen-to-square text-[11px]"></i>
                                    <span>Atur</span>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- USER-FRIENDLY MULTI-CLASS DISTRIBUTION MODAL -->
<div id="distributionModal" class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm flex items-center justify-center z-50 hidden p-3 sm:p-5 lg:p-6 overflow-y-auto">
    <div class="bg-white rounded-3xl shadow-2xl max-w-5xl xl:max-w-6xl w-full p-5 sm:p-7 border border-slate-100 flex flex-col max-h-[95vh] sm:max-h-[92vh] overflow-hidden transition-all duration-300 transform">
        
        <!-- Modal Header -->
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 flex-shrink-0">
            <div class="flex items-center gap-3.5">
                <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-2xl bg-gradient-to-tr from-blue-600 via-indigo-600 to-purple-600 text-white flex items-center justify-center text-xl shadow-lg shadow-blue-500/25 flex-shrink-0">
                    <i class="fa-solid fa-sliders"></i>
                </div>
                <div>
                    <h3 class="font-extrabold text-base sm:text-lg text-slate-800 tracking-tight flex items-center gap-2">
                        Atur Distribusi Tugas Mengajar
                        <span class="hidden sm:inline-block text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-700 border border-blue-200 uppercase tracking-wide">
                            Multi-Kelas 15 Rombel
                        </span>
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-400">Atur alokasi jam mengajar per rombel secara instan dengan form cepat dan terpadu</p>
                </div>
            </div>
            <button type="button" onclick="closeDistributionModal()" class="w-9 h-9 rounded-full bg-slate-100 text-slate-400 hover:text-slate-700 hover:bg-slate-200 flex items-center justify-center transition">
                <i class="fa-solid fa-xmark text-base"></i>
            </button>
        </div>

        <!-- Form Body -->
        <form id="distributionForm" action="{{ route('assignments.batch') }}" method="POST" class="mt-4 flex-1 flex flex-col overflow-hidden space-y-4">
            @csrf

            <!-- Teacher & Subject Selection Row -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 flex-shrink-0 bg-gradient-to-r from-slate-50 via-blue-50/30 to-indigo-50/30 p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-inner">
                <!-- Teacher -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center gap-1.5">
                        <i class="fa-solid fa-user-tie text-blue-600"></i>
                        <span>Nama Guru Pengampu</span>
                    </label>
                    <div class="relative">
                        <select id="modalTeacherSelect" name="teacher_id" required onchange="onTeacherChange()" class="w-full text-xs sm:text-sm font-bold p-3 bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-xs text-slate-800">
                            <option value="">-- Pilih Guru Pengampu --</option>
                            @foreach($teachers as $t)
                                <option value="{{ $t->id }}" data-target="{{ $t->target_hours }}">
                                    {{ $t->order_no ? '[' . $t->order_no . '] ' : '' }}{{ $t->name }} &bull; Target: {{ $t->target_hours }} JP
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Subject -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center gap-1.5">
                        <i class="fa-solid fa-book-open text-indigo-600"></i>
                        <span>Mata Pelajaran yang Diampu</span>
                    </label>
                    <div class="relative">
                        <select id="modalSubjectSelect" name="subject_id" required onchange="onSubjectChange()" class="w-full text-xs sm:text-sm font-bold p-3 bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-xs text-slate-800">
                            <option value="">-- Pilih Mata Pelajaran --</option>
                            @foreach($subjects as $s)
                                <option value="{{ $s->id }}" data-color="{{ $s->color }}">
                                    {{ $s->name }} ({{ $s->category }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Quick Preset Toolbar -->
            <div class="flex flex-wrap items-center justify-between gap-2 flex-shrink-0 text-xs bg-slate-50/80 p-2.5 rounded-2xl border border-slate-200">
                <div class="flex flex-wrap items-center gap-1.5">
                    <span class="text-[11px] font-black text-slate-500 uppercase tracking-wider mr-1 flex items-center gap-1">
                        <i class="fa-solid fa-bolt text-amber-500"></i> Preset Cepat:
                    </span>
                    <button type="button" onclick="applyPreset(10, 2)" class="px-2.5 py-1.5 rounded-lg bg-blue-100/70 hover:bg-blue-200 text-blue-800 font-bold border border-blue-200 transition active:scale-95">
                        +2 JP Kelas X
                    </button>
                    <button type="button" onclick="applyPreset(11, 2)" class="px-2.5 py-1.5 rounded-lg bg-indigo-100/70 hover:bg-indigo-200 text-indigo-800 font-bold border border-indigo-200 transition active:scale-95">
                        +2 JP Kelas XI
                    </button>
                    <button type="button" onclick="applyPreset(12, 2)" class="px-2.5 py-1.5 rounded-lg bg-purple-100/70 hover:bg-purple-200 text-purple-800 font-bold border border-purple-200 transition active:scale-95">
                        +2 JP Kelas XII
                    </button>
                    <div class="h-4 w-px bg-slate-300 mx-1 hidden sm:block"></div>
                    <button type="button" onclick="applyPreset('all', 2)" class="px-3 py-1.5 rounded-lg bg-emerald-100/80 hover:bg-emerald-200 text-emerald-800 font-extrabold border border-emerald-300 transition active:scale-95">
                        +2 JP Seluruh 15 Rombel
                    </button>
                    <button type="button" onclick="applyPreset('all', 3)" class="px-3 py-1.5 rounded-lg bg-teal-100/80 hover:bg-teal-200 text-teal-800 font-extrabold border border-teal-300 transition active:scale-95 hidden sm:inline-block">
                        +3 JP Seluruh 15 Rombel
                    </button>
                </div>
                <div>
                    <button type="button" onclick="applyPreset('reset', 0)" class="px-3 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold border border-rose-200 transition active:scale-95 flex items-center gap-1">
                        <i class="fa-solid fa-rotate-left text-xs"></i> Reset (0 JP)
                    </button>
                </div>
            </div>

            <!-- 15 Classrooms Grid (Scrollable & Roomy) -->
            <div class="flex-1 overflow-y-auto pr-1.5 space-y-5 custom-scrollbar">
                
                <!-- KELAS X -->
                <div class="bg-blue-50/30 rounded-2xl p-3.5 sm:p-4 border border-blue-100/70">
                    <div class="flex items-center justify-between pb-2 mb-3 border-b border-blue-100">
                        <span class="text-xs sm:text-sm font-black text-blue-900 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-blue-600 shadow-xs shadow-blue-500/50"></span>
                            Tingkat Kelas X (Fase E)
                        </span>
                        <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-blue-100 text-blue-700">5 Rombongan Belajar</span>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3">
                        @foreach($classrooms->where('grade', 10) as $c)
                            <div class="p-3.5 rounded-2xl border border-slate-200 bg-white hover:border-blue-400 hover:shadow-md transition-all duration-200 flex flex-col justify-between" id="card_{{ $c->id }}">
                                <div class="flex items-center justify-between mb-2.5">
                                    <label for="check_{{ $c->id }}" class="cursor-pointer font-extrabold text-sm text-slate-800 flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                                        {{ $c->name }}
                                    </label>
                                    <input type="checkbox" id="check_{{ $c->id }}" onchange="onCheckboxToggle({{ $c->id }})" class="w-5 h-5 text-blue-600 rounded-md focus:ring-blue-500 cursor-pointer">
                                </div>
                                <div class="flex items-center gap-1.5 bg-slate-50 p-1 rounded-xl border border-slate-100">
                                    <button type="button" onclick="adjustHours({{ $c->id }}, -1)" class="w-8 h-8 rounded-lg bg-white hover:bg-slate-200 text-slate-700 font-black text-sm flex items-center justify-center border border-slate-200 shadow-2xs transition active:scale-90">-</button>
                                    <input type="number" id="input_{{ $c->id }}" name="classes[{{ $c->id }}]" min="0" max="10" value="0" oninput="onHoursInputChange({{ $c->id }})" class="w-full text-center py-1 text-sm font-black text-slate-800 bg-white border border-slate-200 rounded-lg outline-none focus:ring-2 focus:ring-blue-500">
                                    <button type="button" onclick="adjustHours({{ $c->id }}, 1)" class="w-8 h-8 rounded-lg bg-white hover:bg-slate-200 text-slate-700 font-black text-sm flex items-center justify-center border border-slate-200 shadow-2xs transition active:scale-90">+</button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- KELAS XI -->
                <div class="bg-indigo-50/30 rounded-2xl p-3.5 sm:p-4 border border-indigo-100/70">
                    <div class="flex items-center justify-between pb-2 mb-3 border-b border-indigo-100">
                        <span class="text-xs sm:text-sm font-black text-indigo-900 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-indigo-600 shadow-xs shadow-indigo-500/50"></span>
                            Tingkat Kelas XI (Fase F)
                        </span>
                        <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-indigo-100 text-indigo-700">5 Rombongan Belajar</span>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3">
                        @foreach($classrooms->where('grade', 11) as $c)
                            <div class="p-3.5 rounded-2xl border border-slate-200 bg-white hover:border-indigo-400 hover:shadow-md transition-all duration-200 flex flex-col justify-between" id="card_{{ $c->id }}">
                                <div class="flex items-center justify-between mb-2.5">
                                    <label for="check_{{ $c->id }}" class="cursor-pointer font-extrabold text-sm text-slate-800 flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                                        {{ $c->name }}
                                    </label>
                                    <input type="checkbox" id="check_{{ $c->id }}" onchange="onCheckboxToggle({{ $c->id }})" class="w-5 h-5 text-indigo-600 rounded-md focus:ring-indigo-500 cursor-pointer">
                                </div>
                                <div class="flex items-center gap-1.5 bg-slate-50 p-1 rounded-xl border border-slate-100">
                                    <button type="button" onclick="adjustHours({{ $c->id }}, -1)" class="w-8 h-8 rounded-lg bg-white hover:bg-slate-200 text-slate-700 font-black text-sm flex items-center justify-center border border-slate-200 shadow-2xs transition active:scale-90">-</button>
                                    <input type="number" id="input_{{ $c->id }}" name="classes[{{ $c->id }}]" min="0" max="10" value="0" oninput="onHoursInputChange({{ $c->id }})" class="w-full text-center py-1 text-sm font-black text-slate-800 bg-white border border-slate-200 rounded-lg outline-none focus:ring-2 focus:ring-indigo-500">
                                    <button type="button" onclick="adjustHours({{ $c->id }}, 1)" class="w-8 h-8 rounded-lg bg-white hover:bg-slate-200 text-slate-700 font-black text-sm flex items-center justify-center border border-slate-200 shadow-2xs transition active:scale-90">+</button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- KELAS XII -->
                <div class="bg-purple-50/30 rounded-2xl p-3.5 sm:p-4 border border-purple-100/70">
                    <div class="flex items-center justify-between pb-2 mb-3 border-b border-purple-100">
                        <span class="text-xs sm:text-sm font-black text-purple-900 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-purple-600 shadow-xs shadow-purple-500/50"></span>
                            Tingkat Kelas XII (Fase F Lanjutan)
                        </span>
                        <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-purple-100 text-purple-700">5 Rombongan Belajar</span>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3">
                        @foreach($classrooms->where('grade', 12) as $c)
                            <div class="p-3.5 rounded-2xl border border-slate-200 bg-white hover:border-purple-400 hover:shadow-md transition-all duration-200 flex flex-col justify-between" id="card_{{ $c->id }}">
                                <div class="flex items-center justify-between mb-2.5">
                                    <label for="check_{{ $c->id }}" class="cursor-pointer font-extrabold text-sm text-slate-800 flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                                        {{ $c->name }}
                                    </label>
                                    <input type="checkbox" id="check_{{ $c->id }}" onchange="onCheckboxToggle({{ $c->id }})" class="w-5 h-5 text-purple-600 rounded-md focus:ring-purple-500 cursor-pointer">
                                </div>
                                <div class="flex items-center gap-1.5 bg-slate-50 p-1 rounded-xl border border-slate-100">
                                    <button type="button" onclick="adjustHours({{ $c->id }}, -1)" class="w-8 h-8 rounded-lg bg-white hover:bg-slate-200 text-slate-700 font-black text-sm flex items-center justify-center border border-slate-200 shadow-2xs transition active:scale-90">-</button>
                                    <input type="number" id="input_{{ $c->id }}" name="classes[{{ $c->id }}]" min="0" max="10" value="0" oninput="onHoursInputChange({{ $c->id }})" class="w-full text-center py-1 text-sm font-black text-slate-800 bg-white border border-slate-200 rounded-lg outline-none focus:ring-2 focus:ring-purple-500">
                                    <button type="button" onclick="adjustHours({{ $c->id }}, 1)" class="w-8 h-8 rounded-lg bg-white hover:bg-slate-200 text-slate-700 font-black text-sm flex items-center justify-center border border-slate-200 shadow-2xs transition active:scale-90">+</button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>

            <!-- LIVE TOTAL SUMMARY & FOOTER -->
            <div class="p-4 bg-slate-100/90 rounded-2xl border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3.5 flex-shrink-0">
                <div class="flex flex-wrap items-center gap-3 sm:gap-6 text-xs sm:text-sm">
                    <div class="flex items-baseline gap-1.5">
                        <span class="text-slate-500 font-medium">Total Dialokasikan:</span>
                        <strong id="liveTotalHours" class="text-blue-600 font-black text-base sm:text-lg">0 JP</strong>
                    </div>
                    <div class="flex items-baseline gap-1.5">
                        <span class="text-slate-500 font-medium">Target Beban Guru:</span>
                        <strong id="liveTargetHours" class="text-slate-800 font-black text-base sm:text-lg">0 JP</strong>
                    </div>
                    <div>
                        <span id="targetStatusBadge" class="px-3 py-1 rounded-full text-xs font-black bg-slate-200 text-slate-700 inline-flex items-center gap-1 shadow-2xs">
                            Siap
                        </span>
                    </div>
                </div>

                <div class="flex items-center gap-2.5 justify-end">
                    <button type="button" onclick="closeDistributionModal()" class="px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold text-slate-600 hover:bg-slate-200 hover:text-slate-800 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-extrabold text-xs sm:text-sm shadow-lg shadow-blue-500/25 transition transform active:scale-95 flex items-center gap-2">
                        <i class="fa-solid fa-floppy-disk text-sm"></i> Simpan Distribusi
                    </button>
                </div>
            </div>

        </form>
    </div>
</div>

<script>
    // Existing data passed from server
    const teacherAssignmentsData = @json($teacherAssignmentsMap ?? []);
    const classroomList = @json($classrooms ?? []);

    function openDistributionModal(teacherId = null, teacherName = '', targetHours = 0) {
        const modal = document.getElementById('distributionModal');
        const teacherSelect = document.getElementById('modalTeacherSelect');
        const subjectSelect = document.getElementById('modalSubjectSelect');

        if (teacherId) {
            teacherSelect.value = teacherId;
            document.getElementById('liveTargetHours').innerText = targetHours + ' JP';
        } else {
            teacherSelect.value = '';
            document.getElementById('liveTargetHours').innerText = '0 JP';
        }

        // Try pre-selecting first subject assigned to this teacher if available
        if (teacherId && teacherAssignmentsData[teacherId]) {
            const subjectIds = Object.keys(teacherAssignmentsData[teacherId]);
            if (subjectIds.length > 0) {
                subjectSelect.value = subjectIds[0];
            } else {
                subjectSelect.value = '';
            }
        } else {
            subjectSelect.value = '';
        }

        modal.classList.remove('hidden');
        populateClassInputs();
    }

    function closeDistributionModal() {
        document.getElementById('distributionModal').classList.add('hidden');
    }

    function onTeacherChange() {
        const teacherSelect = document.getElementById('modalTeacherSelect');
        const selectedOption = teacherSelect.options[teacherSelect.selectedIndex];
        const target = selectedOption ? (selectedOption.getAttribute('data-target') || 0) : 0;
        document.getElementById('liveTargetHours').innerText = target + ' JP';

        const teacherId = teacherSelect.value;
        const subjectSelect = document.getElementById('modalSubjectSelect');

        if (teacherId && teacherAssignmentsData[teacherId]) {
            const subjectIds = Object.keys(teacherAssignmentsData[teacherId]);
            if (subjectIds.length > 0) {
                subjectSelect.value = subjectIds[0];
            }
        }
        populateClassInputs();
    }

    function onSubjectChange() {
        populateClassInputs();
    }

    function populateClassInputs() {
        const teacherId = document.getElementById('modalTeacherSelect').value;
        const subjectId = document.getElementById('modalSubjectSelect').value;

        const currentMap = (teacherId && subjectId && teacherAssignmentsData[teacherId] && teacherAssignmentsData[teacherId][subjectId])
            ? teacherAssignmentsData[teacherId][subjectId]
            : {};

        classroomList.forEach(c => {
            const hours = currentMap[c.id] || 0;
            const input = document.getElementById('input_' + c.id);
            const check = document.getElementById('check_' + c.id);
            const card = document.getElementById('card_' + c.id);

            if (input && check && card) {
                input.value = hours;
                check.checked = (hours > 0);
                updateCardStyle(c.id, hours > 0);
            }
        });

        calculateLiveTotal();
    }

    function onCheckboxToggle(classId) {
        const check = document.getElementById('check_' + classId);
        const input = document.getElementById('input_' + classId);

        if (check.checked) {
            if (parseInt(input.value) <= 0) {
                input.value = 2; // Default 2 JP
            }
            updateCardStyle(classId, true);
        } else {
            input.value = 0;
            updateCardStyle(classId, false);
        }
        calculateLiveTotal();
    }

    function onHoursInputChange(classId) {
        const input = document.getElementById('input_' + classId);
        const check = document.getElementById('check_' + classId);
        let val = parseInt(input.value) || 0;
        if (val < 0) val = 0;
        if (val > 10) val = 10;
        input.value = val;

        check.checked = (val > 0);
        updateCardStyle(classId, val > 0);
        calculateLiveTotal();
    }

    function adjustHours(classId, delta) {
        const input = document.getElementById('input_' + classId);
        let val = (parseInt(input.value) || 0) + delta;
        if (val < 0) val = 0;
        if (val > 10) val = 10;
        input.value = val;

        const check = document.getElementById('check_' + classId);
        check.checked = (val > 0);
        updateCardStyle(classId, val > 0);
        calculateLiveTotal();
    }

    function updateCardStyle(classId, isActive) {
        const card = document.getElementById('card_' + classId);
        if (!card) return;
        if (isActive) {
            card.classList.remove('border-slate-200', 'bg-white');
            card.classList.add('bg-blue-50/80', 'border-blue-400', 'shadow-md', 'ring-2', 'ring-blue-500/20');
        } else {
            card.classList.remove('bg-blue-50/80', 'border-blue-400', 'shadow-md', 'ring-2', 'ring-blue-500/20');
            card.classList.add('border-slate-200', 'bg-white');
        }
    }

    function applyPreset(gradeOrAll, hours) {
        classroomList.forEach(c => {
            if (gradeOrAll === 'all' || gradeOrAll === 'reset' || c.grade == gradeOrAll) {
                const targetHours = (gradeOrAll === 'reset') ? 0 : hours;
                const input = document.getElementById('input_' + c.id);
                const check = document.getElementById('check_' + c.id);

                if (input && check) {
                    input.value = targetHours;
                    check.checked = (targetHours > 0);
                    updateCardStyle(c.id, targetHours > 0);
                }
            }
        });
        calculateLiveTotal();
    }

    function calculateLiveTotal() {
        let total = 0;
        classroomList.forEach(c => {
            const input = document.getElementById('input_' + c.id);
            if (input) {
                total += parseInt(input.value) || 0;
            }
        });

        document.getElementById('liveTotalHours').innerText = total + ' JP';

        const teacherSelect = document.getElementById('modalTeacherSelect');
        const selectedOption = teacherSelect.options[teacherSelect.selectedIndex];
        const target = selectedOption ? parseInt(selectedOption.getAttribute('data-target') || 0) : 0;
        const badge = document.getElementById('targetStatusBadge');

        if (target > 0) {
            if (total === target) {
                badge.innerText = 'Sesuai Target (' + total + ' JP)';
                badge.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200';
            } else if (total < target) {
                const diff = target - total;
                badge.innerText = 'Kurang ' + diff + ' JP';
                badge.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-800 border border-amber-200';
            } else {
                const diff = total - target;
                badge.innerText = 'Kelebihan ' + diff + ' JP';
                badge.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-100 text-blue-800 border border-blue-200';
            }
        } else {
            badge.innerText = total + ' JP Dialokasikan';
            badge.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-200 text-slate-700';
        }
    }
</script>
@endsection
