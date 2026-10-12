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

        <button onclick="openAddModal()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-500/20 transition transform active:scale-95">
            <i class="fa-solid fa-plus text-xs"></i>
            <span>Tambah Pembagian Tugas</span>
        </button>
    </div>

    <!-- EXCEL-STYLE DISTRIBUTION MATRIX -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
            <h3 class="font-extrabold text-xs uppercase tracking-wider text-slate-700">
                <i class="fa-solid fa-table-list mr-1.5 text-blue-600"></i> Matriks Distribusi Guru & Kelas (15 Rombel)
            </h3>
            <span class="text-xs text-slate-500">Angka dalam kolom menunjukkan alokasi JP per minggu</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-[11px] text-center border-collapse">
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

                        <th class="py-2.5 px-2 w-14 bg-emerald-950">Total</th>
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

                            <td class="py-2 px-1 font-extrabold text-xs {{ $rowTotal >= 24 ? 'text-emerald-700 bg-emerald-50/50' : 'text-amber-600 bg-amber-50/50' }}">
                                {{ $rowTotal }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- ADD MODAL -->
<div id="addModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center z-50 hidden p-4">
    <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full p-6 border border-slate-100">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h3 class="font-bold text-base text-slate-800">Tambah Penugasan Mengajar</h3>
            <button onclick="closeAddModal()" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <form action="{{ route('assignments.store') }}" method="POST" class="mt-4 space-y-3.5">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Pilih Guru</label>
                <div class="relative mb-1.5">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" placeholder="Ketik cari nama guru..." oninput="filterSelectOptions(this, 'teacherSelect')" class="w-full pl-8 pr-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <select id="teacherSelect" name="teacher_id" required class="w-full text-xs p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="">-- Pilih Guru --</option>
                    @foreach($teachers as $t)
                        <option value="{{ $t->id }}">{{ $t->order_no ? '[' . $t->order_no . '] ' : '' }}{{ $t->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Pilih Mata Pelajaran</label>
                <div class="relative mb-1.5">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" placeholder="Ketik cari mata pelajaran..." oninput="filterSelectOptions(this, 'subjectSelect')" class="w-full pl-8 pr-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <select id="subjectSelect" name="subject_id" required class="w-full text-xs p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="">-- Pilih Mapel --</option>
                    @foreach($subjects as $s)
                        <option value="{{ $s->id }}">{{ $s->name }} ({{ $s->category }})</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Kelas (Rombel)</label>
                    <select name="classroom_id" required class="w-full text-xs p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                        @foreach($classrooms as $c)
                            <option value="{{ $c->id }}">Kelas {{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Alokasi Jam (JP/mg)</label>
                    <input type="number" name="hours_per_week" value="2" min="1" max="10" required class="w-full text-xs p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
            </div>

            <div class="pt-3 flex items-center justify-end gap-2 border-t border-slate-100">
                <button type="button" onclick="closeAddModal()" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100">Batal</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-500/20">Simpan Penugasan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAddModal() { document.getElementById('addModal').classList.remove('hidden'); }
    function closeAddModal() { document.getElementById('addModal').classList.add('hidden'); }

    function filterSelectOptions(input, selectId) {
        const query = input.value.toLowerCase();
        const select = document.getElementById(selectId);
        const options = select.options;
        for (let i = 1; i < options.length; i++) {
            const text = options[i].text.toLowerCase();
            if (text.includes(query)) {
                options[i].style.display = '';
            } else {
                options[i].style.display = 'none';
            }
        }
    }
</script>
@endsection
