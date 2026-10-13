@extends('layouts.app')

@section('title', 'Jadwal Guru Piket')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    
    <form id="piketForm" action="{{ route('pikets.update') }}" method="POST">
        @csrf

        <!-- HEADER BAR -->
        <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 shadow-xs mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div class="flex items-start sm:items-center space-x-3 sm:space-x-4">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-indigo-600 to-blue-600 text-white flex items-center justify-center text-xl shadow-lg shadow-indigo-500/25 flex-shrink-0">
                    <i class="fa-solid fa-clipboard-user"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">Jadwal Guru Piket Harian</h1>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                            Search & Select Aktif
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                        Pilih guru piket harian tanpa mengetik manual. Perubahan otomatis tersinkronisasi ke seluruh tabel jadwal dan cetak jadwal.
                    </p>
                </div>
            </div>

            <!-- Quick Navigation Actions: Sisakan Reset dan Simpan Saja di Atas -->
            <div class="flex items-center gap-2.5">
                <button type="button" onclick="resetToInitial()" class="px-4 py-2.5 rounded-xl border border-slate-200 hover:bg-slate-100 text-slate-700 text-xs font-bold transition inline-flex items-center gap-2 shadow-2xs">
                    <i class="fa-solid fa-rotate-left text-slate-500"></i>
                    <span>Reset</span>
                </button>
                <button type="submit" form="piketForm" class="px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white text-xs font-extrabold rounded-xl shadow-md shadow-indigo-600/30 transition inline-flex items-center gap-2 transform active:scale-95">
                    <i class="fa-solid fa-floppy-disk text-sm"></i>
                    <span>Simpan Guru Piket</span>
                </button>
            </div>
        </div>

        <!-- INFO CARD / BANNER -->
        <div class="mt-5 p-3.5 bg-slate-50 border border-slate-200/80 rounded-xl flex flex-col md:flex-row md:items-center justify-between gap-3 text-xs text-slate-600">
            <div class="flex items-center gap-2.5">
                <div class="w-6 h-6 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center flex-shrink-0 text-xs font-bold">
                    <i class="fa-solid fa-circle-info"></i>
                </div>
                <span><strong>Panduan Cepat:</strong> Klik pada kolom pencarian di hari yang diinginkan, cari nama atau kode guru, lalu klik nama guru untuk menambahkannya ke daftar piket. Klik tombol tanda silang (<i class="fa-solid fa-xmark text-rose-500"></i>) untuk menghapus.</span>
            </div>
            <div class="flex items-center gap-3 font-semibold text-slate-700 flex-shrink-0">
                <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Total Guru Terdaftar: {{ $allTeachers->count() }} Orang</span>
            </div>
        </div>
    </div>

        <!-- 5 DAYS CONTAINER -->
        <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-5">
            @foreach($days as $day)
                @php
                    $dayInfo = $piketData[$day] ?? null;
                    $dayTeachers = $dayInfo['teachers'] ?? collect([]);
                    $dayThemes = [
                        'Senin' => ['border' => 'border-blue-200', 'badge' => 'bg-blue-50 text-blue-700 border-blue-200', 'accent' => 'from-blue-600 to-indigo-600', 'headerBg' => 'bg-blue-50/60'],
                        'Selasa' => ['border' => 'border-emerald-200', 'badge' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'accent' => 'from-emerald-600 to-teal-600', 'headerBg' => 'bg-emerald-50/60'],
                        'Rabu' => ['border' => 'border-amber-200', 'badge' => 'bg-amber-50 text-amber-700 border-amber-200', 'accent' => 'from-amber-600 to-orange-600', 'headerBg' => 'bg-amber-50/60'],
                        'Kamis' => ['border' => 'border-purple-200', 'badge' => 'bg-purple-50 text-purple-700 border-purple-200', 'accent' => 'from-purple-600 to-indigo-600', 'headerBg' => 'bg-purple-50/60'],
                        'Jumat' => ['border' => 'border-rose-200', 'badge' => 'bg-rose-50 text-rose-700 border-rose-200', 'accent' => 'from-rose-600 to-pink-600', 'headerBg' => 'bg-rose-50/60'],
                    ];
                    $theme = $dayThemes[$day] ?? ['border' => 'border-slate-200', 'badge' => 'bg-slate-50 text-slate-700 border-slate-200', 'accent' => 'from-slate-700 to-slate-900', 'headerBg' => 'bg-slate-50'];
                @endphp

                <div class="bg-white rounded-2xl border {{ $theme['border'] }} shadow-xs flex flex-col overflow-hidden transition-all hover:shadow-md" id="cardDay-{{ $day }}">
                    
                    <!-- DAY HEADER -->
                    <div class="p-4 sm:p-5 {{ $theme['headerBg'] }} border-b {{ $theme['border'] }} flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr {{ $theme['accent'] }} text-white flex items-center justify-center font-extrabold text-sm shadow-md">
                                {{ substr($day, 0, 3) }}
                            </div>
                            <div>
                                <h2 class="font-extrabold text-base text-slate-800">Hari {{ $day }}</h2>
                                <p class="text-[11px] text-slate-500 font-medium">Tugas Piket Pembelajaran</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <span id="badgeCount-{{ $day }}" class="text-[11px] font-bold px-2.5 py-1 rounded-full border {{ $theme['badge'] }}">
                                {{ $dayTeachers->count() }} Guru
                            </span>
                            <button type="button" 
                                    onclick="clearDay('{{ $day }}')" 
                                    title="Kosongkan Guru Piket Hari Ini"
                                    class="w-7 h-7 rounded-lg bg-white/80 hover:bg-rose-50 hover:text-rose-600 text-slate-400 border border-slate-200 flex items-center justify-center transition">
                                <i class="fa-solid fa-trash-can text-xs"></i>
                            </button>
                        </div>
                    </div>

                    <!-- SEARCH & SELECT DROPDOWN COMBOBOX -->
                    <div class="p-4 sm:p-5 border-b border-slate-100 bg-white relative">
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            <i class="fa-solid fa-magnifying-glass text-indigo-500 mr-1"></i> Tambah Guru Piket:
                        </label>
                        
                        <div class="relative">
                            <div class="relative flex items-center">
                                <input type="text" 
                                       id="searchInput-{{ $day }}"
                                       autocomplete="off"
                                       placeholder="Cari nama atau kode guru..." 
                                       onfocus="showDropdown('{{ $day }}')"
                                       oninput="filterTeachers('{{ $day }}')"
                                       class="w-full text-xs font-medium pl-9 pr-8 py-2.5 bg-slate-50 hover:bg-slate-100/80 focus:bg-white border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 transition placeholder:text-slate-400">
                                <i class="fa-solid fa-user-plus text-slate-400 absolute left-3 text-xs pointer-events-none"></i>
                                <button type="button" 
                                        onclick="clearSearchInput('{{ $day }}')" 
                                        id="clearSearchBtn-{{ $day }}"
                                        class="hidden absolute right-2.5 text-slate-400 hover:text-slate-600 p-1">
                                    <i class="fa-solid fa-xmark text-xs"></i>
                                </button>
                            </div>

                            <!-- POPUP DROPDOWN LIST -->
                            <div id="dropdownList-{{ $day }}" 
                                 class="hidden absolute left-0 right-0 top-full mt-1.5 bg-white border border-slate-200 rounded-xl shadow-xl z-30 max-h-56 overflow-y-auto divide-y divide-slate-100 transition-all">
                                <!-- Options rendered via JS -->
                            </div>
                        </div>
                    </div>

                    <!-- SELECTED TEACHERS LIST CONTAINER -->
                    <div class="p-4 sm:p-5 flex-1 flex flex-col bg-slate-50/50">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Daftar Guru Bertugas:</span>
                            <span class="text-[11px] text-slate-400 italic" id="emptyNotice-{{ $day }}" style="{{ $dayTeachers->count() > 0 ? 'display: none;' : '' }}">Belum ada guru piket</span>
                        </div>

                        <!-- TEACHER CHIPS / TAGS LIST -->
                        <div id="selectedList-{{ $day }}" class="space-y-2 flex-1 min-h-[140px]">
                            @forelse($dayTeachers as $teacher)
                                <div class="teacher-item flex items-center justify-between p-2.5 bg-white border border-slate-200/90 rounded-xl shadow-2xs hover:border-indigo-300 transition group" 
                                     data-teacher-id="{{ $teacher->id }}" 
                                     data-day="{{ $day }}">
                                    <input type="hidden" name="teachers[{{ $day }}][]" value="{{ $teacher->id }}">
                                    
                                    <div class="flex items-center space-x-3 overflow-hidden">
                                        <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-700 border border-indigo-100 flex items-center justify-center font-bold text-xs flex-shrink-0 group-hover:bg-indigo-600 group-hover:text-white transition">
                                            {{ $teacher->order_no ? sprintf('%02d', $teacher->order_no) : strtoupper(substr($teacher->name, 0, 1)) }}
                                        </div>
                                        <div class="truncate">
                                            <p class="text-xs font-bold text-slate-800 truncate leading-snug">{{ $teacher->name }}</p>
                                            <p class="text-[10px] text-slate-400 truncate">
                                                {{ $teacher->additional_role ? $teacher->additional_role : 'Guru Pengajar' }}
                                            </p>
                                        </div>
                                    </div>

                                    <button type="button" 
                                            onclick="removeTeacher('{{ $day }}', {{ $teacher->id }})" 
                                            title="Hapus dari Hari {{ $day }}"
                                            class="w-7 h-7 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 flex items-center justify-center transition flex-shrink-0 ml-2">
                                        <i class="fa-solid fa-xmark text-sm"></i>
                                    </button>
                                </div>
                            @empty
                                <!-- Empty state placeholder rendered by JS -->
                            @endforelse
                        </div>

                        <!-- DAY SUMMARY PREVIEW -->
                        <div class="mt-4 pt-3 border-t border-slate-200/60 flex items-center justify-between text-[11px] text-slate-500">
                            <span>Format SK:</span>
                            <span class="font-semibold text-slate-700 truncate max-w-[200px]" id="previewText-{{ $day }}">
                                {{ $dayInfo['raw_string'] ?: '-' }}
                            </span>
                        </div>
                    </div>

                </div>
            @endforeach
        </div>
    </form>
</div>

<!-- JAVASCRIPT LOGIC FOR LIVE SEARCH & SELECT -->
@push('scripts')
<script>
    // Master Teacher Data from Controller
    const allTeachers = @json($allTeachers);
    const daysList = @json($days);

    // Filter teachers for dropdown on input
    function filterTeachers(day) {
        const input = document.getElementById(`searchInput-${day}`);
        const query = (input.value || '').trim().toLowerCase();
        const clearBtn = document.getElementById(`clearSearchBtn-${day}`);
        const dropdown = document.getElementById(`dropdownList-${day}`);

        if (query.length > 0) {
            clearBtn.classList.remove('hidden');
        } else {
            clearBtn.classList.add('hidden');
        }

        // Get currently selected teacher IDs in this day
        const selectedIds = getSelectedTeacherIds(day);

        // Filter allTeachers
        const filtered = allTeachers.filter(t => {
            const nameMatch = (t.name || '').toLowerCase().includes(query);
            const orderMatch = t.order_no && String(t.order_no).includes(query);
            const roleMatch = (t.additional_role || '').toLowerCase().includes(query);
            return nameMatch || orderMatch || roleMatch;
        });

        renderDropdownOptions(day, filtered, selectedIds);
        dropdown.classList.remove('hidden');
    }

    function showDropdown(day) {
        filterTeachers(day);
    }

    function clearSearchInput(day) {
        const input = document.getElementById(`searchInput-${day}`);
        input.value = '';
        input.focus();
        filterTeachers(day);
    }

    // Render options in the floating dropdown list
    function renderDropdownOptions(day, list, selectedIds) {
        const dropdown = document.getElementById(`dropdownList-${day}`);
        dropdown.innerHTML = '';

        if (list.length === 0) {
            dropdown.innerHTML = `
                <div class="p-3 text-center text-xs text-slate-400">
                    <i class="fa-solid fa-user-slash mr-1"></i> Tidak ada guru yang cocok
                </div>
            `;
            return;
        }

        list.slice(0, 15).forEach(t => {
            const isAlreadySelected = selectedIds.includes(t.id);
            const item = document.createElement('div');
            item.className = `p-2.5 flex items-center justify-between cursor-pointer transition ${
                isAlreadySelected ? 'bg-slate-100 opacity-60 pointer-events-none' : 'hover:bg-indigo-50/80 bg-white'
            }`;

            const orderStr = t.order_no ? String(t.order_no).padStart(2, '0') : t.name.charAt(0).toUpperCase();

            item.innerHTML = `
                <div class="flex items-center space-x-2.5 overflow-hidden">
                    <div class="w-7 h-7 rounded-lg ${isAlreadySelected ? 'bg-slate-200 text-slate-500' : 'bg-indigo-100 text-indigo-700'} flex items-center justify-center font-bold text-[11px] flex-shrink-0">
                        ${orderStr}
                    </div>
                    <div class="truncate">
                        <p class="text-xs font-bold text-slate-800 truncate">${escapeHtml(t.name)}</p>
                        <p class="text-[10px] text-slate-400 truncate">${escapeHtml(t.additional_role || 'Guru Pengajar')}</p>
                    </div>
                </div>
                <div>
                    ${isAlreadySelected 
                        ? `<span class="text-[10px] font-semibold text-slate-400 bg-slate-200/80 px-2 py-0.5 rounded-full"><i class="fa-solid fa-check mr-1"></i>Ditambahkan</span>` 
                        : `<button type="button" class="px-2.5 py-1 bg-indigo-600 hover:bg-indigo-700 text-white text-[11px] font-bold rounded-lg shadow-xs transition flex items-center gap-1">
                             <i class="fa-solid fa-plus text-[10px]"></i> Pilih
                           </button>`
                    }
                </div>
            `;

            if (!isAlreadySelected) {
                item.onclick = (e) => {
                    e.preventDefault();
                    addTeacher(day, t);
                };
            }

            dropdown.appendChild(item);
        });

        if (list.length > 15) {
            const moreItem = document.createElement('div');
            moreItem.className = 'p-2 text-center text-[10px] text-slate-400 bg-slate-50 font-medium';
            moreItem.innerText = `Menampilkan 15 dari ${list.length} guru... Ketik nama lebih spesifik`;
            dropdown.appendChild(moreItem);
        }
    }

    // Add teacher to selected list
    function addTeacher(day, teacher) {
        const listContainer = document.getElementById(`selectedList-${day}`);
        const selectedIds = getSelectedTeacherIds(day);

        if (selectedIds.includes(teacher.id)) {
            return;
        }

        const orderStr = teacher.order_no ? String(teacher.order_no).padStart(2, '0') : teacher.name.charAt(0).toUpperCase();

        const card = document.createElement('div');
        card.className = 'teacher-item flex items-center justify-between p-2.5 bg-white border border-slate-200/90 rounded-xl shadow-2xs hover:border-indigo-300 transition group animate-fade-in';
        card.setAttribute('data-teacher-id', teacher.id);
        card.setAttribute('data-day', day);

        card.innerHTML = `
            <input type="hidden" name="teachers[${day}][]" value="${teacher.id}">
            <div class="flex items-center space-x-3 overflow-hidden">
                <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-700 border border-indigo-100 flex items-center justify-center font-bold text-xs flex-shrink-0 group-hover:bg-indigo-600 group-hover:text-white transition">
                    ${orderStr}
                </div>
                <div class="truncate">
                    <p class="text-xs font-bold text-slate-800 truncate leading-snug">${escapeHtml(teacher.name)}</p>
                    <p class="text-[10px] text-slate-400 truncate">${escapeHtml(teacher.additional_role || 'Guru Pengajar')}</p>
                </div>
            </div>
            <button type="button" 
                    onclick="removeTeacher('${day}', ${teacher.id})" 
                    title="Hapus dari Hari ${day}"
                    class="w-7 h-7 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 flex items-center justify-center transition flex-shrink-0 ml-2">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        `;

        listContainer.appendChild(card);

        // Clear input and close dropdown
        const searchInput = document.getElementById(`searchInput-${day}`);
        searchInput.value = '';
        document.getElementById(`clearSearchBtn-${day}`).classList.add('hidden');
        document.getElementById(`dropdownList-${day}`).classList.add('hidden');

        updateDayCountAndPreview(day);
    }

    // Remove teacher from selected list
    function removeTeacher(day, teacherId) {
        const item = document.querySelector(`.teacher-item[data-day="${day}"][data-teacher-id="${teacherId}"]`);
        if (item) {
            item.remove();
            updateDayCountAndPreview(day);
        }
    }

    // Clear all teachers for a day
    function clearDay(day) {
        if (!confirm(`Kosongkan seluruh guru piket untuk Hari ${day}?`)) return;
        const listContainer = document.getElementById(`selectedList-${day}`);
        listContainer.innerHTML = '';
        updateDayCountAndPreview(day);
    }

    // Helper: get array of currently selected teacher IDs for a day
    function getSelectedTeacherIds(day) {
        const inputs = document.querySelectorAll(`input[name="teachers[${day}][]"]`);
        return Array.from(inputs).map(inp => parseInt(inp.value));
    }

    // Helper: update badge count and string preview
    function updateDayCountAndPreview(day) {
        const ids = getSelectedTeacherIds(day);
        const count = ids.length;

        // Update badge
        const badge = document.getElementById(`badgeCount-${day}`);
        if (badge) badge.innerText = `${count} Guru`;

        // Update empty notice
        const emptyNotice = document.getElementById(`emptyNotice-${day}`);
        if (emptyNotice) {
            emptyNotice.style.display = count === 0 ? 'inline' : 'none';
        }

        // Update string preview
        const preview = document.getElementById(`previewText-${day}`);
        if (preview) {
            if (count === 0) {
                preview.innerText = '-';
            } else {
                const names = ids.map(id => {
                    const t = allTeachers.find(x => x.id === id);
                    return t ? t.name : '';
                }).filter(Boolean);
                preview.innerText = names.join(', ');
            }
        }
    }

    function resetToInitial() {
        if (confirm('Kembalikan data form ke kondisi awal saat halaman dibuka?')) {
            window.location.reload();
        }
    }

    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    // Close dropdown when clicked outside
    document.addEventListener('click', function(e) {
        daysList.forEach(day => {
            const dropdown = document.getElementById(`dropdownList-${day}`);
            const input = document.getElementById(`searchInput-${day}`);
            const clearBtn = document.getElementById(`clearSearchBtn-${day}`);
            if (dropdown && !dropdown.contains(e.target) && e.target !== input && e.target !== clearBtn) {
                dropdown.classList.add('hidden');
            }
        });
    });

    // Initialize counts on page load
    document.addEventListener('DOMContentLoaded', () => {
        daysList.forEach(day => {
            updateDayCountAndPreview(day);
        });
    });
</script>
@endpush
@endsection
