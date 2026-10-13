@extends('layouts.app')

@section('title', 'Profil & Manajemen Akun - SIKAD MAN 20 Jakarta')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto pb-10">

    <!-- PAGE HEADER CARD -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white flex items-center justify-center font-extrabold text-2xl shadow-lg shadow-blue-500/20 flex-shrink-0">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h2 class="text-xl sm:text-2xl font-black text-slate-800 tracking-tight">{{ $user->name }}</h2>
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-blue-100 text-blue-700 border border-blue-200">
                        Administrator
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200 flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        Aktif
                    </span>
                </div>
                <p class="text-xs sm:text-sm text-slate-500 mt-1 flex items-center gap-2">
                    <i class="fa-solid fa-envelope text-slate-400"></i>
                    <span>{{ $user->email }}</span>
                    <span class="text-slate-300">•</span>
                    <span>Terdaftar sejak: {{ $user->created_at ? $user->created_at->format('d M Y') : '12 Okt 2026' }}</span>
                </p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <div class="px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-right">
                <span class="text-[10px] uppercase tracking-wider font-extrabold text-slate-400 block">Total Akun Sistem</span>
                <span class="text-base font-black text-slate-800 flex items-center justify-end gap-1.5">
                    <i class="fa-solid fa-users text-blue-600 text-xs"></i>
                    {{ $allUsers->count() }} Akun
                </span>
            </div>
        </div>
    </div>

    <!-- MAIN TWO COLUMN GRID: PROFILE & PASSWORD -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- CARD 1: EDIT PROFILE & EMAIL -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-4 mb-5 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg font-bold">
                            <i class="fa-solid fa-user-pen"></i>
                        </div>
                        <div>
                            <h3 class="font-black text-slate-800 text-base">Informasi Akun & Email</h3>
                            <p class="text-xs text-slate-500">Perbarui nama pengguna dan alamat email untuk login</p>
                        </div>
                    </div>
                </div>

                <form action="{{ route('profile.update') }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <!-- Nama Lengkap -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            Nama Lengkap / Identitas Pengguna:
                        </label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs">
                                <i class="fa-solid fa-user"></i>
                            </span>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                   class="w-full pl-9 pr-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-blue-500 focus:bg-white outline-none transition @error('name') border-rose-400 @enderror"
                                   placeholder="Contoh: Administrator SIKAD MAN 20">
                        </div>
                        @error('name')
                            <p class="text-[11px] text-rose-500 font-semibold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Alamat Email -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            Alamat Email (Untuk Login Aplikasi):
                        </label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs">
                                <i class="fa-solid fa-at"></i>
                            </span>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                   class="w-full pl-9 pr-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-blue-500 focus:bg-white outline-none transition @error('email') border-rose-400 @enderror"
                                   placeholder="nama@man20.sch.id">
                        </div>
                        @error('email')
                            <p class="text-[11px] text-rose-500 font-semibold mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-[11px] text-slate-400 mt-1">
                            Email ini digunakan saat masuk ke sistem jadwal. Pastikan selalu aktif.
                        </p>
                    </div>

                    <div class="pt-3">
                        <button type="submit" class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl transition shadow-md shadow-blue-500/20 flex items-center justify-center gap-2 transform active:scale-95">
                            <i class="fa-solid fa-floppy-disk"></i>
                            <span>Simpan Perubahan Akun</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- CARD 2: GANTI PASSWORD -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-4 mb-5 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg font-bold">
                            <i class="fa-solid fa-shield-halved"></i>
                        </div>
                        <div>
                            <h3 class="font-black text-slate-800 text-base">Ubah Kata Sandi</h3>
                            <p class="text-xs text-slate-500">Ganti password secara berkala untuk menjaga keamanan</p>
                        </div>
                    </div>
                </div>

                <form action="{{ route('profile.password') }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <!-- Password Saat Ini -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            Password Saat Ini:
                        </label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs">
                                <i class="fa-solid fa-key"></i>
                            </span>
                            <input type="password" id="current_password" name="current_password" required
                                   class="w-full pl-9 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-indigo-500 focus:bg-white outline-none transition @error('current_password') border-rose-400 @enderror"
                                   placeholder="Masukkan password yang aktif saat ini">
                            <button type="button" onclick="togglePasswordVisibility('current_password', this)" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                        @error('current_password')
                            <p class="text-[11px] text-rose-500 font-semibold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Baru -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            Password Baru (Minimal 6 Karakter):
                        </label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs">
                                <i class="fa-solid fa-lock"></i>
                            </span>
                            <input type="password" id="new_password" name="password" required
                                   class="w-full pl-9 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-indigo-500 focus:bg-white outline-none transition @error('password') border-rose-400 @enderror"
                                   placeholder="Ketik password baru">
                            <button type="button" onclick="togglePasswordVisibility('new_password', this)" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="text-[11px] text-rose-500 font-semibold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Konfirmasi Password Baru -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            Konfirmasi Password Baru:
                        </label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs">
                                <i class="fa-solid fa-circle-check"></i>
                            </span>
                            <input type="password" id="new_password_confirmation" name="password_confirmation" required
                                   class="w-full pl-9 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-indigo-500 focus:bg-white outline-none transition"
                                   placeholder="Ulangi kembali password baru">
                            <button type="button" onclick="togglePasswordVisibility('new_password_confirmation', this)" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="pt-3">
                        <button type="submit" class="w-full py-2.5 px-4 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl transition shadow-md shadow-indigo-500/20 flex items-center justify-center gap-2 transform active:scale-95">
                            <i class="fa-solid fa-lock"></i>
                            <span>Perbarui Password</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <!-- BOTTOM ROW: BUAT AKUN BARU & DAFTAR AKUN PENGGUNA -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- CARD 3: FORM BUAT AKUN BARU -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 lg:col-span-1 flex flex-col justify-between">
            <div>
                <div class="flex items-center gap-3 pb-4 mb-5 border-b border-slate-100">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg font-bold">
                        <i class="fa-solid fa-user-plus"></i>
                    </div>
                    <div>
                        <h3 class="font-black text-slate-800 text-base">Buat Akun Baru</h3>
                        <p class="text-xs text-slate-500">Tambah operator/pengguna baru</p>
                    </div>
                </div>

                <form action="{{ route('profile.users.store') }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            Nama Lengkap Pengguna:
                        </label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs">
                                <i class="fa-solid fa-id-badge"></i>
                            </span>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                   class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-emerald-500 focus:bg-white outline-none transition"
                                   placeholder="Contoh: Guru Piket / Operator Kurikulum">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            Email Akun Baru:
                        </label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs">
                                <i class="fa-solid fa-envelope"></i>
                            </span>
                            <input type="email" name="email" value="{{ old('email') }}" required
                                   class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-emerald-500 focus:bg-white outline-none transition"
                                   placeholder="operator@man20.sch.id">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            Password Awal (Min. 6 Karakter):
                        </label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs">
                                <i class="fa-solid fa-lock"></i>
                            </span>
                            <input type="password" id="create_password" name="password" required
                                   class="w-full pl-9 pr-10 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-emerald-500 focus:bg-white outline-none transition"
                                   placeholder="Kata sandi akun baru">
                            <button type="button" onclick="togglePasswordVisibility('create_password', this)" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            Konfirmasi Password:
                        </label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs">
                                <i class="fa-solid fa-check"></i>
                            </span>
                            <input type="password" id="create_password_confirmation" name="password_confirmation" required
                                   class="w-full pl-9 pr-10 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-emerald-500 focus:bg-white outline-none transition"
                                   placeholder="Ulangi kata sandi">
                            <button type="button" onclick="togglePasswordVisibility('create_password_confirmation', this)" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition shadow-md shadow-emerald-500/20 flex items-center justify-center gap-2 transform active:scale-95">
                            <i class="fa-solid fa-plus-circle"></i>
                            <span>Daftarkan Akun Baru</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- CARD 4: DAFTAR AKUN PENGGUNA TERDAFTAR -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 lg:col-span-2">
            <div class="flex items-center justify-between pb-4 mb-5 border-b border-slate-100 flex-wrap gap-2">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center text-lg font-bold">
                        <i class="fa-solid fa-address-book"></i>
                    </div>
                    <div>
                        <h3 class="font-black text-slate-800 text-base">Daftar Akun Pengguna</h3>
                        <p class="text-xs text-slate-500">Seluruh akun yang memiliki hak akses login ke aplikasi</p>
                    </div>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200">
                    {{ $allUsers->count() }} Pengguna Terdaftar
                </span>
            </div>

            <!-- Table of users -->
            <div class="overflow-x-auto rounded-2xl border border-slate-100">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50 text-slate-700 font-extrabold uppercase text-[10px] tracking-wider border-b border-slate-200">
                            <th class="py-3 px-3.5">Pengguna</th>
                            <th class="py-3 px-3.5">Email Login</th>
                            <th class="py-3 px-3.5">Status</th>
                            <th class="py-3 px-3.5 text-right">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($allUsers as $u)
                            <tr class="hover:bg-slate-50/60 transition {{ $u->id === $user->id ? 'bg-blue-50/30' : '' }}">
                                <td class="py-3 px-3.5">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-slate-700 to-slate-900 text-white flex items-center justify-center font-bold text-xs flex-shrink-0 shadow-2xs">
                                            {{ strtoupper(substr($u->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <span class="font-extrabold text-slate-800 block text-xs">{{ $u->name }}</span>
                                            <span class="text-[10px] text-slate-400">ID: #{{ $u->id }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-3.5 font-semibold text-slate-600">
                                    {{ $u->email }}
                                </td>
                                <td class="py-3 px-3.5">
                                    @if($u->id === $user->id)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-100 text-blue-700 border border-blue-200">
                                            <i class="fa-solid fa-user-check text-[9px]"></i> Akun Anda
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                            Operator
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-3.5 text-right">
                                    @if($u->id === $user->id)
                                        <span class="text-[11px] text-slate-400 italic">Sedang Digunakan</span>
                                    @else
                                        <form action="{{ route('profile.users.destroy', $u->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun pengguna {{ $u->name }} ({{ $u->email }})? Tindakan ini tidak dapat dibatalkan.');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 px-2.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 text-xs font-bold border border-rose-200 transition inline-flex items-center gap-1" title="Hapus akun pengguna ini">
                                                <i class="fa-solid fa-trash-can text-[10px]"></i>
                                                <span>Hapus</span>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Security Notice Banner -->
            <div class="mt-4 p-3.5 bg-blue-50 border border-blue-100 rounded-2xl flex items-start gap-3 text-xs text-blue-800">
                <i class="fa-solid fa-circle-info text-blue-600 text-base mt-0.5 flex-shrink-0"></i>
                <div class="leading-relaxed">
                    <strong>Catatan Keamanan:</strong> Setiap akun yang terdaftar memiliki hak akses penuh untuk melihat, mengatur, dan mencetak jadwal pelajaran madrasah. Berikan akses hanya kepada staf, operator, atau pimpinan yang berwenang.
                </div>
            </div>
        </div>

    </div>

</div>

<!-- SCRIPT TOGGLE SHOW/HIDE PASSWORD -->
<script>
    function togglePasswordVisibility(inputId, button) {
        const input = document.getElementById(inputId);
        const icon = button.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
</script>
@endsection
