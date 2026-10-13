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
        <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama Resmi Madrasah</label>
                    <input type="text" name="school_name" value="{{ $settings['school_name'] ?? 'MADRASAH ALIYAH NEGERI 20 JAKARTA TIMUR' }}" required
                           class="w-full text-xs p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Alamat Madrasah</label>
                    <input type="text" name="school_address" value="{{ $settings['school_address'] ?? 'Jl. Rawa Kuning No. 4 RT 011/07 Pulo Gebang, Cakung, Jakarta Timur 13950' }}"
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

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Wakil Kepala Bidang Akademik / Kurikulum</label>
                    <input type="text" name="waka_kurikulum" value="{{ $settings['waka_kurikulum'] ?? 'TEMY YULIANTI, S.Pd., M.Pd.' }}"
                           class="w-full text-xs p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Tempat & Tanggal Titimangsa Dokumen</label>
                    <input type="text" name="date_signed" value="{{ $settings['date_signed'] ?? 'Jakarta, 13 Juli 2026' }}"
                           class="w-full text-xs p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none"
                           placeholder="Contoh: Jakarta, 13 Juli 2026">
                </div>

                <!-- Input Keterangan Revisi & Mulai Berlaku -->
                <div class="md:col-span-2 bg-gradient-to-r from-blue-50 to-indigo-50 p-4 rounded-2xl border border-blue-200 shadow-2xs">
                    <div class="flex items-center gap-2 mb-1.5">
                        <i class="fa-solid fa-clock-rotate-left text-blue-600 text-sm"></i>
                        <label class="block text-xs font-black text-slate-800">
                            Keterangan Revisi & Mulai Berlaku Jadwal Pelajaran
                        </label>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-200/70 text-blue-900 ml-auto">
                            Dicetak di Dokumen
                        </span>
                    </div>
                    <input type="text" name="schedule_revision" value="{{ $settings['schedule_revision'] ?? 'REVISI4_BERLAKU MULAI : Senin, 12 Oktober 2026' }}" required
                           class="w-full text-xs p-3 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none font-bold text-slate-800 shadow-2xs"
                           placeholder="Contoh: REVISI4_BERLAKU MULAI : Senin, 12 Oktober 2026">
                    <p class="text-[11px] text-slate-600 mt-1.5">
                        Teks ini dicetak tepat di bawah judul jadwal pelajaran pada lembar master matrix, cetak kelas, dan ekspor excel/PDF (Contoh format: <em>REVISI4_BERLAKU MULAI : Senin, 12 Oktober 2026</em>).
                    </p>
                </div>
            </div>

            <!-- SECTION: UPLOAD TANDA TANGAN & STEMPEL -->
            <div class="pt-6 border-t border-slate-200">
                <div class="mb-4">
                    <h3 class="text-sm font-extrabold text-slate-800 flex items-center gap-2">
                        <i class="fa-solid fa-signature text-blue-600"></i>
                        Unggah Tanda Tangan & Stempel Resmi Madrasah
                    </h3>
                    <p class="text-[11px] text-slate-400 mt-0.5">
                        Gambar ini akan otomatis dicetak pada kolom tanda tangan dokumen jadwal kelas, guru, dan jadwal lengkap. Disarankan format PNG transparan.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    
                    <!-- 1. Tanda Tangan Kepala Madrasah -->
                    <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl flex flex-col justify-between">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Tanda Tangan Kepala Madrasah</label>
                            <p class="text-[10px] text-slate-400 mb-3">Drs. SODIKIN, M.Si.</p>
                            
                            <div class="w-full h-28 bg-white border border-dashed border-slate-300 rounded-xl flex items-center justify-center p-2 mb-3 relative overflow-hidden">
                                @if(!empty($settings['principal_signature']) && file_exists(public_path($settings['principal_signature'])))
                                    <img src="{{ asset($settings['principal_signature']) }}" alt="TTD Kepala Madrasah" class="max-h-full max-w-full object-contain">
                                @else
                                    <div class="text-center text-slate-300">
                                        <i class="fa-solid fa-signature text-3xl mb-1 block"></i>
                                        <span class="text-[10px]">Belum ada tanda tangan</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div>
                            <input type="file" name="principal_signature" accept="image/*" class="w-full text-[11px] file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-[11px] file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 text-slate-500 mb-2">
                            @if(!empty($settings['principal_signature']))
                                <label class="inline-flex items-center gap-1.5 text-[11px] text-rose-600 font-semibold cursor-pointer">
                                    <input type="checkbox" name="remove_principal_signature" value="1" class="rounded text-rose-600">
                                    <span>Hapus tanda tangan ini</span>
                                </label>
                            @endif
                        </div>
                    </div>

                    <!-- 2. Stempel Resmi Madrasah -->
                    <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl flex flex-col justify-between">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Stempel Resmi Madrasah</label>
                            <p class="text-[10px] text-slate-400 mb-3">Cap Stempel MAN 20 Jakarta</p>
                            
                            <div class="w-full h-28 bg-white border border-dashed border-slate-300 rounded-xl flex items-center justify-center p-2 mb-3 relative overflow-hidden">
                                @if(!empty($settings['school_stamp']) && file_exists(public_path($settings['school_stamp'])))
                                    <img src="{{ asset($settings['school_stamp']) }}" alt="Stempel Madrasah" class="max-h-full max-w-full object-contain">
                                @else
                                    <div class="text-center text-slate-300">
                                        <i class="fa-solid fa-stamp text-3xl mb-1 block"></i>
                                        <span class="text-[10px]">Belum ada stempel</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div>
                            <input type="file" name="school_stamp" accept="image/*" class="w-full text-[11px] file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-[11px] file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 text-slate-500 mb-2">
                            @if(!empty($settings['school_stamp']))
                                <label class="inline-flex items-center gap-1.5 text-[11px] text-rose-600 font-semibold cursor-pointer">
                                    <input type="checkbox" name="remove_school_stamp" value="1" class="rounded text-rose-600">
                                    <span>Hapus stempel ini</span>
                                </label>
                            @endif
                        </div>
                    </div>

                    <!-- 3. Tanda Tangan Waka Kurikulum -->
                    <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl flex flex-col justify-between">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Tanda Tangan Waka Kurikulum</label>
                            <p class="text-[10px] text-slate-400 mb-3">TEMY YULIANTI, S.Pd., M.Pd.</p>
                            
                            <div class="w-full h-28 bg-white border border-dashed border-slate-300 rounded-xl flex items-center justify-center p-2 mb-3 relative overflow-hidden">
                                @if(!empty($settings['academic_head_signature']) && file_exists(public_path($settings['academic_head_signature'])))
                                    <img src="{{ asset($settings['academic_head_signature']) }}" alt="TTD Waka Kurikulum" class="max-h-full max-w-full object-contain">
                                @else
                                    <div class="text-center text-slate-300">
                                        <i class="fa-solid fa-file-signature text-3xl mb-1 block"></i>
                                        <span class="text-[10px]">Belum ada tanda tangan</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div>
                            <input type="file" name="academic_head_signature" accept="image/*" class="w-full text-[11px] file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-[11px] file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 text-slate-500 mb-2">
                            @if(!empty($settings['academic_head_signature']))
                                <label class="inline-flex items-center gap-1.5 text-[11px] text-rose-600 font-semibold cursor-pointer">
                                    <input type="checkbox" name="remove_academic_head_signature" value="1" class="rounded text-rose-600">
                                    <span>Hapus tanda tangan ini</span>
                                </label>
                            @endif
                        </div>
                    </div>

                </div>
            </div>

            <div class="pt-5 border-t border-slate-100 flex items-center justify-end">
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-500/20 transition transform active:scale-95">
                    <i class="fa-solid fa-floppy-disk mr-1.5"></i> Simpan Pengaturan & Tanda Tangan
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
