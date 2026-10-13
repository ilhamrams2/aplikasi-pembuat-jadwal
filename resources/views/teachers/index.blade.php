@extends('layouts.app')

@section('title', 'Data Guru - SIKAD MAN 20 Jakarta')

@section('content')
<div class="space-y-6">

    <!-- HEADER & ACTIONS -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-800 tracking-tight">Data Guru & Tenaga Pengajar</h2>
            <p class="text-xs text-slate-500 mt-0.5">Kelola data seluruh 36 guru dan tugas tambahan madrasah</p>
        </div>

        <div class="flex items-center gap-3">
            <button onclick="openAddModal()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-500/20 transition transform active:scale-95">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Tambah Guru</span>
            </button>
        </div>
    </div>

    <!-- FILTER & SEARCH -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
        <form method="GET" action="{{ route('teachers.index') }}" class="flex flex-1 items-center gap-3 w-full">
            <div class="relative flex-1 max-w-md">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama guru, NIP, atau tugas..."
                       class="w-full pl-9 pr-4 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
            <button type="submit" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl">
                Cari
            </button>
            @if(request('search'))
                <a href="{{ route('teachers.index') }}" class="text-xs text-rose-500 hover:underline">Reset</a>
            @endif
        </form>

        <div class="text-xs text-slate-500 font-medium">
            Total: <strong class="text-slate-800">{{ $teachers->total() }} Guru</strong>
        </div>
    </div>

    <!-- TABLE -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto w-full">
            <table class="w-full text-xs text-left min-w-[720px]">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 border-b border-slate-200 uppercase font-semibold text-[10px]">
                        <th class="py-3 px-4 w-12 text-center">No</th>
                        <th class="py-3 px-4">Nama Lengkap Guru</th>
                        <th class="py-3 px-4">NIP / Kontak</th>
                        <th class="py-3 px-4">Tugas Tambahan</th>
                        <th class="py-3 px-4 text-center">Beban Target</th>
                        <th class="py-3 px-4 text-center">Beban Mengajar</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($teachers as $t)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-3.5 px-4 text-center font-bold text-slate-400">
                                {{ $t->order_no ?: $loop->iteration }}
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-extrabold text-slate-800 text-sm">{{ $t->name }}</div>
                                <div class="text-[11px] text-slate-400 mt-0.5">
                                    {{ $t->assignments->count() }} Mata Pelajaran &bull; 
                                    @php
                                        $uniqueClasses = $t->assignments->pluck('classroom.name')->unique()->implode(', ');
                                    @endphp
                                    Kelas: {{ $uniqueClasses ?: '-' }}
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-slate-600 font-mono text-[11px]">
                                <div>{{ $t->nip ?: '-' }}</div>
                                <div class="text-[10px] text-slate-400 font-sans">{{ $t->phone ?: '' }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                @if($t->additional_role)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                        {{ $t->additional_role }}
                                    </span>
                                @else
                                    <span class="text-slate-300">-</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center font-bold text-slate-600">
                                {{ $t->target_hours }} JP
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @php $actual = $t->total_teaching_hours; @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold {{ $actual >= 24 ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                                    {{ $actual }} JP
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right space-x-1.5 whitespace-nowrap">
                                <a href="{{ route('schedules.index', ['type' => 'teacher', 'teacher_id' => $t->id]) }}" class="px-2.5 py-1 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-lg font-semibold text-[11px] transition" title="Lihat Jadwal">
                                    <i class="fa-regular fa-calendar-days"></i>
                                </a>
                                <button onclick="openEditModal({{ json_encode($t) }})" class="px-2.5 py-1 bg-slate-100 text-slate-600 hover:bg-slate-200 rounded-lg font-semibold text-[11px] transition" title="Edit">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <form action="{{ route('teachers.destroy', $t->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus data guru {{ $t->name }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2 py-1 bg-rose-50 text-rose-600 hover:bg-rose-100 rounded-lg font-semibold text-[11px] transition" title="Hapus">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">Tidak ada data guru yang ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $teachers->links() }}
        </div>
    </div>

</div>

<!-- ADD MODAL -->
<div id="addModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center z-50 hidden p-4">
    <div class="bg-white rounded-3xl shadow-2xl max-w-lg w-full p-6 border border-slate-100">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h3 class="font-bold text-base text-slate-800">Tambah Guru Baru</h3>
            <button onclick="closeAddModal()" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <form action="{{ route('teachers.store') }}" method="POST" enctype="multipart/form-data" class="mt-4 space-y-3.5">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Nama Lengkap & Gelar</label>
                <input type="text" name="name" required class="w-full text-xs p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Contoh: Drs. AHMAD, M.Pd.">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">NIP (Opsional)</label>
                    <input type="text" name="nip" class="w-full text-xs p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none" placeholder="19800101...">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Beban Target Jam (JP)</label>
                    <input type="number" name="target_hours" value="24" required class="w-full text-xs p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Tugas Tambahan</label>
                <input type="text" name="additional_role" class="w-full text-xs p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Contoh: Wali Kelas X.1, Waka Sarpras, Piket">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Nomor Telepon / WhatsApp</label>
                <input type="text" name="phone" class="w-full text-xs p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none" placeholder="0812...">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Unggah Tanda Tangan Guru (Opsional)</label>
                <input type="file" name="signature_image" accept="image/*" class="w-full text-[11px] file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-[11px] file:font-bold file:bg-blue-50 file:text-blue-700 text-slate-500">
            </div>

            <div class="pt-3 flex items-center justify-end gap-2 border-t border-slate-100">
                <button type="button" onclick="closeAddModal()" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100">Batal</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-500/20">Simpan Data Guru</button>
            </div>
        </form>
    </div>
</div>

<!-- EDIT MODAL -->
<div id="editModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center z-50 hidden p-4">
    <div class="bg-white rounded-3xl shadow-2xl max-w-lg w-full p-6 border border-slate-100">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h3 class="font-bold text-base text-slate-800">Edit Data Guru</h3>
            <button onclick="closeEditModal()" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <form id="editForm" method="POST" enctype="multipart/form-data" class="mt-4 space-y-3.5">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Nama Lengkap & Gelar</label>
                <input type="text" id="editName" name="name" required class="w-full text-xs p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">NIP (Opsional)</label>
                    <input type="text" id="editNip" name="nip" class="w-full text-xs p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Beban Target Jam (JP)</label>
                    <input type="number" id="editTarget" name="target_hours" required class="w-full text-xs p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Tugas Tambahan</label>
                <input type="text" id="editRole" name="additional_role" class="w-full text-xs p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Nomor Telepon / WhatsApp</label>
                <input type="text" id="editPhone" name="phone" class="w-full text-xs p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
            </div>

            <!-- Tanda Tangan Guru Preview & Upload -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Tanda Tangan Guru</label>
                <div id="editSignaturePreview" class="hidden mb-2 p-2.5 bg-slate-50 border border-slate-200 rounded-xl flex items-center justify-between">
                    <img id="editSignatureImg" src="" alt="TTD Guru" class="max-h-12 object-contain">
                    <label class="inline-flex items-center gap-1.5 text-xs text-rose-600 font-semibold cursor-pointer">
                        <input type="checkbox" name="remove_signature_image" value="1" class="rounded text-rose-600">
                        <span>Hapus TTD</span>
                    </label>
                </div>
                <input type="file" name="signature_image" accept="image/*" class="w-full text-[11px] file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-[11px] file:font-bold file:bg-blue-50 file:text-blue-700 text-slate-500">
            </div>

            <div class="pt-3 flex items-center justify-end gap-2 border-t border-slate-100">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100">Batal</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-500/20">Perbarui Data</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAddModal() { document.getElementById('addModal').classList.remove('hidden'); }
    function closeAddModal() { document.getElementById('addModal').classList.add('hidden'); }
    
    function openEditModal(teacher) {
        document.getElementById('editForm').action = '/teachers/' + teacher.id;
        document.getElementById('editName').value = teacher.name;
        document.getElementById('editNip').value = teacher.nip || '';
        document.getElementById('editTarget').value = teacher.target_hours;
        document.getElementById('editRole').value = teacher.additional_role || '';
        document.getElementById('editPhone').value = teacher.phone || '';

        const previewContainer = document.getElementById('editSignaturePreview');
        const previewImg = document.getElementById('editSignatureImg');
        if (teacher.signature_image) {
            previewImg.src = '/' + teacher.signature_image;
            previewContainer.classList.remove('hidden');
        } else {
            previewContainer.classList.add('hidden');
        }

        document.getElementById('editModal').classList.remove('hidden');
    }
    function closeEditModal() { document.getElementById('editModal').classList.add('hidden'); }
</script>
@endsection
