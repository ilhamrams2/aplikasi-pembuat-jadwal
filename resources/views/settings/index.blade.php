@extends('layouts.app')

@section('title', 'Pengaturan Madrasah - SIKAD MAN 20 Jakarta')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- HEADER -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <h2 class="text-xl font-extrabold text-slate-800 tracking-tight">Pengaturan Identitas Madrasah</h2>
        <p class="text-xs text-slate-500 mt-1">Data identitas ini digunakan pada kop surat, laporan jadwal, dan format cetak resmi.</p>
    </div>

    <!-- FORM -->
    <div class="bg-white p-8 rounded-2xl border border-slate-200 shadow-sm">
        <form action="{{ route('settings.update') }}" method="POST" class="space-y-5">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama Resmi Madrasah</label>
                    <input type="text" name="school_name" value="{{ $settings['school_name'] ?? 'MADRASAH ALIYAH NEGERI 20 JAKARTA TIMUR' }}" required
                           class="w-full text-xs p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Alamat Madrasah</label>
                    <input type="text" name="school_address" value="{{ $settings['school_address'] ?? 'Jl. Swadaya No. 20, Duren Sawit, Jakarta Timur' }}"
                           class="w-full text-xs p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Tahun Pelajaran</label>
                    <input type="text" name="academic_year" value="{{ $settings['academic_year'] ?? '2026/2027' }}" required
                           class="w-full text-xs p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Semester</label>
                    <select name="semester" required class="w-full text-xs p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                        <option value="Ganjil" {{ ($settings['semester'] ?? '') === 'Ganjil' ? 'selected' : '' }}>Ganjil (Juli - Desember)</option>
                        <option value="Genap" {{ ($settings['semester'] ?? '') === 'Genap' ? 'selected' : '' }}>Genap (Januari - Juni)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama Kepala Madrasah</label>
                    <input type="text" name="principal_name" value="{{ $settings['principal_name'] ?? 'Drs. SODIKIN, M.Si.' }}" required
                           class="w-full text-xs p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">NIP Kepala Madrasah</label>
                    <input type="text" name="principal_nip" value="{{ $settings['principal_nip'] ?? '196805101994031002' }}"
                           class="w-full text-xs p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Wakil Kepala Bidang Akademik / Kurikulum</label>
                    <input type="text" name="waka_kurikulum" value="{{ $settings['waka_kurikulum'] ?? 'TEMY YULIANTI, S.Pd., M.Pd.' }}"
                           class="w-full text-xs p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
            </div>

            <div class="pt-5 border-t border-slate-100 flex items-center justify-end">
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-500/20 transition transform active:scale-95">
                    <i class="fa-solid fa-floppy-disk mr-1.5"></i> Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
