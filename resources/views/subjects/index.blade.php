@extends('layouts.app')

@section('title', 'Mata Pelajaran - SIKAD MAN 20 Jakarta')

@section('content')
<div class="space-y-6">

    <!-- HEADER & ACTIONS -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-800 tracking-tight">Mata Pelajaran</h2>
            <p class="text-xs text-slate-500 mt-0.5">Daftar kurikulum dan pengelompokan mata pelajaran</p>
        </div>

        <button onclick="openAddModal()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-500/20 transition transform active:scale-95">
            <i class="fa-solid fa-plus text-xs"></i>
            <span>Tambah Mapel</span>
        </button>
    </div>

    <!-- FILTER & SEARCH -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
        <form method="GET" action="{{ route('subjects.index') }}" class="flex flex-1 flex-wrap items-center gap-3 w-full">
            <div class="relative flex-1 max-w-xs">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau kode mapel..."
                       class="w-full pl-9 pr-4 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
            </div>

            <select name="category" onchange="this.form.submit()" class="text-xs font-semibold px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                <option value="">-- Semua Kategori --</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                @endforeach
            </select>

            @if(request('search') || request('category'))
                <a href="{{ route('subjects.index') }}" class="text-xs text-rose-500 hover:underline">Reset Filter</a>
            @endif
        </form>

        <div class="text-xs text-slate-500 font-medium">
            Total: <strong class="text-slate-800">{{ $subjects->total() }} Mapel</strong>
        </div>
    </div>

    <!-- TABLE -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto w-full">
            <table class="w-full text-xs text-left min-w-[650px]">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 border-b border-slate-200 uppercase font-semibold text-[10px]">
                        <th class="py-3 px-4 w-12 text-center">No</th>
                        <th class="py-3 px-4">Kode Mapel</th>
                        <th class="py-3 px-4">Nama Mata Pelajaran</th>
                        <th class="py-3 px-4">Kategori / Kelompok</th>
                        <th class="py-3 px-4 text-center">Warna Label</th>
                        <th class="py-3 px-4 text-center">Total Distribusi Kelas</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($subjects as $s)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-3.5 px-4 text-center font-bold text-slate-400">
                                {{ $loop->iteration }}
                            </td>
                            <td class="py-3.5 px-4 font-mono font-bold text-slate-700">
                                <span class="px-2 py-0.5 rounded bg-slate-100 border border-slate-200">{{ $s->code }}</span>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-extrabold text-slate-800 text-sm flex items-center gap-2">
                                    <span class="w-3 h-3 rounded-full flex-shrink-0" style="background-color: {{ $s->color }};"></span>
                                    {{ $s->name }}
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                    {{ $s->category }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center font-mono text-[11px] text-slate-500">
                                {{ $s->color }}
                            </td>
                            <td class="py-3.5 px-4 text-center font-bold text-slate-700">
                                {{ $s->assignments_count }} Kelas
                            </td>
                            <td class="py-3.5 px-4 text-right space-x-1.5 whitespace-nowrap">
                                <button onclick="openEditModal({{ json_encode($s) }})" class="px-2.5 py-1 bg-slate-100 text-slate-600 hover:bg-slate-200 rounded-lg font-semibold text-[11px] transition" title="Edit">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <form action="{{ route('subjects.destroy', $s->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus mata pelajaran {{ $s->name }}?');">
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
                            <td colspan="7" class="py-8 text-center text-slate-400">Tidak ada mata pelajaran yang ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $subjects->links() }}
        </div>
    </div>

</div>

<!-- ADD MODAL -->
<div id="addModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center z-50 hidden p-4">
    <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full p-6 border border-slate-100">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h3 class="font-bold text-base text-slate-800">Tambah Mata Pelajaran</h3>
            <button onclick="closeAddModal()" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <form action="{{ route('subjects.store') }}" method="POST" class="mt-4 space-y-3.5">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Kode Mapel (Singkat)</label>
                <input type="text" name="code" required class="w-full text-xs p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none font-mono" placeholder="Contoh: MAT-W, BIO-TL">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Nama Mata Pelajaran</label>
                <input type="text" name="name" required class="w-full text-xs p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Contoh: Matematika Tingkat Lanjut">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Kategori / Kelompok</label>
                <select name="category" required class="w-full text-xs p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="Wajib">Wajib (Umum)</option>
                    <option value="Tingkat Lanjut">Tingkat Lanjut (Peminatan)</option>
                    <option value="Agama">Pendidikan Agama Islam</option>
                    <option value="Muatan Lokal">Muatan Lokal</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Warna Label Jadwal</label>
                <input type="color" name="color" value="#2563EB" class="w-full h-10 p-1 bg-slate-50 border border-slate-200 rounded-xl cursor-pointer">
            </div>

            <div class="pt-3 flex items-center justify-end gap-2 border-t border-slate-100">
                <button type="button" onclick="closeAddModal()" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100">Batal</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-500/20">Simpan Mapel</button>
            </div>
        </form>
    </div>
</div>

<!-- EDIT MODAL -->
<div id="editModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center z-50 hidden p-4">
    <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full p-6 border border-slate-100">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h3 class="font-bold text-base text-slate-800">Edit Mata Pelajaran</h3>
            <button onclick="closeEditModal()" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <form id="editForm" method="POST" class="mt-4 space-y-3.5">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Kode Mapel</label>
                <input type="text" id="editCode" name="code" required class="w-full text-xs p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none font-mono">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Nama Mata Pelajaran</label>
                <input type="text" id="editName" name="name" required class="w-full text-xs p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Kategori / Kelompok</label>
                <select id="editCategory" name="category" required class="w-full text-xs p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="Wajib">Wajib (Umum)</option>
                    <option value="Tingkat Lanjut">Tingkat Lanjut (Peminatan)</option>
                    <option value="Agama">Pendidikan Agama Islam</option>
                    <option value="Muatan Lokal">Muatan Lokal</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Warna Label Jadwal</label>
                <input type="color" id="editColor" name="color" class="w-full h-10 p-1 bg-slate-50 border border-slate-200 rounded-xl cursor-pointer">
            </div>

            <div class="pt-3 flex items-center justify-end gap-2 border-t border-slate-100">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100">Batal</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-500/20">Perbarui Mapel</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAddModal() { document.getElementById('addModal').classList.remove('hidden'); }
    function closeAddModal() { document.getElementById('addModal').classList.add('hidden'); }
    
    function openEditModal(sub) {
        document.getElementById('editForm').action = '/subjects/' + sub.id;
        document.getElementById('editCode').value = sub.code;
        document.getElementById('editName').value = sub.name;
        document.getElementById('editCategory').value = sub.category;
        document.getElementById('editColor').value = sub.color || '#2563EB';
        document.getElementById('editModal').classList.remove('hidden');
    }
    function closeEditModal() { document.getElementById('editModal').classList.add('hidden'); }
</script>
@endsection
