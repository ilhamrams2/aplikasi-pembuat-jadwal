@extends('layouts.app')

@section('title', 'Kelas & Rombel - SIKAD MAN 20 Jakarta')

@section('content')
<div class="space-y-6">

    <!-- HEADER & ACTIONS -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-800 tracking-tight">Rombongan Belajar (Kelas)</h2>
            <p class="text-xs text-slate-500 mt-0.5">Daftar 15 Rombel Kelas X, XI, dan XII beserta Wali Kelas</p>
        </div>

        <button onclick="openAddModal()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-500/20 transition transform active:scale-95">
            <i class="fa-solid fa-plus text-xs"></i>
            <span>Tambah Rombel</span>
        </button>
    </div>

    <!-- GRADE SUMMARY CARDS -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @foreach([10 => 'Kelas X (Fase E)', 11 => 'Kelas XI (Fase F)', 12 => 'Kelas XII (Fase F Lanjutan)'] as $grade => $title)
            @php $count = $classrooms->where('grade', $grade)->count(); @endphp
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-blue-600 uppercase tracking-wider">Tingkat {{ $grade }}</span>
                        <h4 class="font-extrabold text-slate-800 text-base mt-0.5">{{ $title }}</h4>
                        <p class="text-xs text-slate-400 mt-1">{{ $count }} Rombongan Belajar</p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-slate-50 text-slate-700 flex items-center justify-center font-extrabold text-sm border border-slate-200">
                        {{ $grade }}
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- TABLE -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 border-b border-slate-200 uppercase font-semibold text-[10px]">
                        <th class="py-3 px-4 w-12 text-center">No</th>
                        <th class="py-3 px-4">Nama Kelas</th>
                        <th class="py-3 px-4">Tingkat</th>
                        <th class="py-3 px-4">Wali Kelas</th>
                        <th class="py-3 px-4 text-center">Beban Kurikulum</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($classrooms as $c)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-3.5 px-4 text-center font-bold text-slate-400">
                                {{ $loop->iteration }}
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-extrabold text-slate-800 text-sm">Kelas {{ $c->name }}</span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-700">
                                    Tingkat {{ $c->grade }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4">
                                @if($c->homeroomTeacher)
                                    <div class="font-semibold text-slate-800">{{ $c->homeroomTeacher->name }}</div>
                                    <div class="text-[10px] text-slate-400">NIP: {{ $c->homeroomTeacher->nip ?: '-' }}</div>
                                @else
                                    <span class="text-amber-500 font-medium">Belum Ditentukan</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center font-extrabold text-blue-600">
                                {{ $c->total_hours ?: 50 }} JP / Minggu
                            </td>
                            <td class="py-3.5 px-4 text-right space-x-1.5 whitespace-nowrap">
                                <a href="{{ route('schedules.index', ['type' => 'class', 'classroom_id' => $c->id]) }}" class="px-2.5 py-1 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-lg font-semibold text-[11px] transition">
                                    <i class="fa-solid fa-calendar-days mr-1"></i> Jadwal
                                </a>
                                <button onclick="openEditModal({{ json_encode($c) }})" class="px-2.5 py-1 bg-slate-100 text-slate-600 hover:bg-slate-200 rounded-lg font-semibold text-[11px] transition">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <form action="{{ route('classrooms.destroy', $c->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus kelas {{ $c->name }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2 py-1 bg-rose-50 text-rose-600 hover:bg-rose-100 rounded-lg font-semibold text-[11px] transition">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
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
            <h3 class="font-bold text-base text-slate-800">Tambah Kelas Baru</h3>
            <button onclick="closeAddModal()" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <form action="{{ route('classrooms.store') }}" method="POST" class="mt-4 space-y-3.5">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Nama Kelas (Rombel)</label>
                <input type="text" name="name" required class="w-full text-xs p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Contoh: X.6 atau XI.6">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Tingkat</label>
                <select name="grade" required class="w-full text-xs p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="10">Kelas 10 (Fase E)</option>
                    <option value="11">Kelas 11 (Fase F)</option>
                    <option value="12">Kelas 12 (Fase F Lanjutan)</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Wali Kelas</label>
                <select name="homeroom_teacher_id" class="w-full text-xs p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="">-- Pilih Wali Kelas (Opsional) --</option>
                    @foreach($teachers as $t)
                        <option value="{{ $t->id }}">{{ $t->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="pt-3 flex items-center justify-end gap-2 border-t border-slate-100">
                <button type="button" onclick="closeAddModal()" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100">Batal</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-500/20">Simpan Kelas</button>
            </div>
        </form>
    </div>
</div>

<!-- EDIT MODAL -->
<div id="editModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center z-50 hidden p-4">
    <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full p-6 border border-slate-100">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h3 class="font-bold text-base text-slate-800">Edit Kelas</h3>
            <button onclick="closeEditModal()" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <form id="editForm" method="POST" class="mt-4 space-y-3.5">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Nama Kelas</label>
                <input type="text" id="editName" name="name" required class="w-full text-xs p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Tingkat</label>
                <select id="editGrade" name="grade" required class="w-full text-xs p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="10">Kelas 10 (Fase E)</option>
                    <option value="11">Kelas 11 (Fase F)</option>
                    <option value="12">Kelas 12 (Fase F Lanjutan)</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Wali Kelas</label>
                <select id="editTeacher" name="homeroom_teacher_id" class="w-full text-xs p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="">-- Pilih Wali Kelas (Opsional) --</option>
                    @foreach($teachers as $t)
                        <option value="{{ $t->id }}">{{ $t->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="pt-3 flex items-center justify-end gap-2 border-t border-slate-100">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100">Batal</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-500/20">Perbarui Kelas</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAddModal() { document.getElementById('addModal').classList.remove('hidden'); }
    function closeAddModal() { document.getElementById('addModal').classList.add('hidden'); }
    
    function openEditModal(cls) {
        document.getElementById('editForm').action = '/classrooms/' + cls.id;
        document.getElementById('editName').value = cls.name;
        document.getElementById('editGrade').value = cls.grade;
        document.getElementById('editTeacher').value = cls.homeroom_teacher_id || '';
        document.getElementById('editModal').classList.remove('hidden');
    }
    function closeEditModal() { document.getElementById('editModal').classList.add('hidden'); }
</script>
@endsection
