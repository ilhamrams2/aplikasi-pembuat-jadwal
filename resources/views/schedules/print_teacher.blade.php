<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Mengajar Guru {{ $teacher->name }} - MAN 20 Jakarta Timur</title>
    
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* PAGE SETUP: STRICT A4 LANDSCAPE (1 HALAMAN) */
        @page {
            size: A4 landscape;
            margin: 7mm 10mm;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body {
            font-family: 'Plus Jakarta Sans', Arial, sans-serif;
            font-size: 10px;
            color: #0f172a;
            margin: 0;
            padding: 12px;
            background-color: #f1f5f9;
        }

        /* Screen Wrapper */
        .page-container {
            width: 277mm;
            max-width: 100%;
            margin: 0 auto;
            background: #ffffff;
            padding: 8mm 10mm;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        /* Screen Action Toolbar */
        .toolbar {
            width: 277mm;
            max-width: 100%;
            margin: 0 auto 12px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #ffffff;
            padding: 10px 16px;
            border-radius: 12px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.06);
            border: 1px solid #e2e8f0;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
        }
        .btn-back { background: #f1f5f9; color: #475569; }
        .btn-back:hover { background: #e2e8f0; }
        .btn-print { background: #2563eb; color: #ffffff; box-shadow: 0 4px 12px rgba(37,99,235,0.25); }
        .btn-print:hover { background: #1d4ed8; }

        /* KOP SURAT RESMI MADRASAH (SESUAI STANDAR KEMENAG) */
        .kop-surat {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 3px double #000000;
            padding-bottom: 6px;
            margin-bottom: 8px;
            position: relative;
        }

        .kop-logo-left {
            width: 58px;
            height: 58px;
            object-fit: contain;
        }

        .kop-logo-right {
            width: 58px;
            height: 58px;
            object-fit: contain;
        }

        .kop-text {
            text-align: center;
            flex: 1;
            padding: 0 12px;
            line-height: 1.25;
        }

        .kop-text .instansi-1 {
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.5px;
            color: #000;
            margin: 0;
            text-transform: uppercase;
        }

        .kop-text .instansi-2 {
            font-size: 11px;
            font-weight: 700;
            color: #000;
            margin: 1px 0;
            text-transform: uppercase;
        }

        .kop-text .nama-madrasah {
            font-size: 15px;
            font-weight: 900;
            color: #000;
            letter-spacing: 0.8px;
            margin: 2px 0;
            text-transform: uppercase;
        }

        .kop-text .alamat {
            font-size: 8.5px;
            font-weight: 500;
            color: #222;
            margin: 0;
        }

        /* DOCUMENT TITLE & METADATA */
        .doc-title-box {
            text-align: center;
            margin-bottom: 8px;
        }

        .doc-title {
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            margin: 0;
            color: #000;
        }

        .doc-subtitle {
            font-size: 9.5px;
            font-weight: 600;
            color: #334155;
            margin: 2px 0 0 0;
            text-transform: uppercase;
        }

        .meta-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            padding: 5px 12px;
            border-radius: 6px;
            margin-bottom: 8px;
            font-size: 10px;
            font-weight: 700;
        }

        .meta-item span {
            color: #64748b;
            font-weight: 600;
            margin-right: 4px;
        }

        /* TIMETABLE */
        table.timetable {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
            table-layout: fixed;
            border: 1.5px solid #000;
        }

        table.timetable th,
        table.timetable td {
            border: 1px solid #000;
            padding: 4px 2px;
            text-align: center;
            vertical-align: middle;
            word-wrap: break-word;
        }

        table.timetable thead th {
            background-color: #e2e8f0;
            font-weight: 800;
            color: #0f172a;
            font-size: 8.5px;
            line-height: 1.2;
        }

        .th-time {
            font-size: 7.5px;
            font-weight: 500;
            color: #475569;
            display: block;
            margin-top: 1px;
        }

        .day-col {
            width: 48px;
            background: #f8fafc;
            font-weight: 900;
            font-size: 9px;
            letter-spacing: 0.5px;
            color: #0f172a;
        }

        .cell-content {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 38px;
        }

        .subj-name {
            font-weight: 800;
            font-size: 8.5px;
            color: #0f172a;
            line-height: 1.15;
            text-transform: uppercase;
        }

        .class-badge {
            display: inline-block;
            font-size: 8px;
            font-weight: 800;
            color: #1d4ed8;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            padding: 1px 6px;
            border-radius: 4px;
            margin-top: 2px;
        }

        .th-break {
            background-color: #f1f5f9 !important;
            color: #1e293b !important;
            font-size: 8px !important;
            width: 58px;
            border: 1px solid #000;
        }

        .td-break {
            background-color: #f8fafc;
            vertical-align: middle;
            text-align: center;
            padding: 4px 2px !important;
            border: 1px solid #000;
        }

        .break-box {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 2px;
        }

        .break-tag {
            font-size: 7px;
            font-weight: 800;
            color: #475569;
            letter-spacing: 0.4px;
            text-transform: uppercase;
        }

        .break-title {
            font-size: 7.5px;
            font-weight: 900;
            color: #0f172a;
            line-height: 1.15;
            text-transform: uppercase;
        }

        .break-time-sub {
            font-size: 6.8px;
            font-weight: 600;
            color: #64748b;
        }

        .schedule-note-bar {
            margin-top: 5px;
            font-size: 7.5px;
            color: #475569;
            display: flex;
            justify-content: space-between;
            font-style: italic;
        }

        /* SIGNATURE SECTION */
        .sign-container {
            margin-top: 12px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            page-break-inside: avoid;
        }

        .sign-box {
            width: 250px;
            text-align: center;
            font-size: 9.5px;
            line-height: 1.35;
        }

        .sign-box .role-title {
            font-weight: 600;
            color: #334155;
            min-height: 28px;
        }

        /* Image Wrapper with overlay capability for stamp */
        .sign-image-wrapper {
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 2px 0;
            position: relative;
        }

        .sign-img {
            max-height: 58px;
            max-width: 140px;
            object-fit: contain;
            position: relative;
            z-index: 2;
        }

        .stamp-img {
            max-height: 60px;
            max-width: 60px;
            object-fit: contain;
            position: absolute;
            left: 20px;
            opacity: 0.85;
            z-index: 1;
            mix-blend-mode: multiply;
        }

        .sign-box .person-name {
            font-weight: 800;
            font-size: 10px;
            color: #0f172a;
            text-decoration: underline;
        }

        .sign-box .person-nip {
            font-size: 8.5px;
            color: #475569;
            font-weight: 600;
        }

        /* PRINT STYLES */
        @media print {
            body {
                background: #ffffff;
                padding: 0;
                margin: 0;
            }
            .page-container {
                box-shadow: none;
                border-radius: 0;
                padding: 0;
                width: 100%;
                margin: 0;
            }
            .no-print {
                display: none !important;
            }
        }

        @if(isset($isPdf) && $isPdf)
        body {
            background: #ffffff !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        .page-container {
            box-shadow: none !important;
            border-radius: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            margin: 0 !important;
        }
        .toolbar {
            display: none !important;
        }
        @endif
    </style>
</head>
<body>

    @if(!isset($isPdf) || !$isPdf)
    <!-- SCREEN ONLY TOOLBAR -->
    <div class="toolbar no-print">
        <div style="display: flex; align-items: center; gap: 8px;">
            <a href="{{ route('schedules.index') }}" class="btn-action btn-back">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Jadwal
            </a>
            <span style="font-size: 11px; font-weight: 700; color: #475569;">
                Jadwal Guru: {{ $teacher->name }} &bull; Format Standar A4 Landscape
            </span>
        </div>
        <button onclick="window.print()" class="btn-action btn-print">
            <i class="fa-solid fa-print"></i> Cetak Dokumen (A4)
        </button>
    </div>
    @endif

    <!-- MAIN A4 CANVAS -->
    <div class="page-container">
        
        <!-- KOP SURAT RESMI -->
        <div class="kop-surat">
            <img src="{{ asset('images/logo_kemenag.png') }}" alt="Logo Kemenag" class="kop-logo-left">
            
            <div class="kop-text">
                <div class="instansi-1">KEMENTERIAN AGAMA REPUBLIK INDONESIA</div>
                <div class="instansi-2">KANTOR KEMENTERIAN AGAMA KOTA JAKARTA TIMUR</div>
                <div class="nama-madrasah">{{ $settings['school_name'] ?? 'MADRASAH ALIYAH NEGERI 20 JAKARTA' }}</div>
                <div class="alamat">{{ $settings['school_address'] ?? 'Jl. Rawa Kuning No. 4 RT 011/07 Pulo Gebang, Cakung, Jakarta Timur 13950 &bull; Telp. (021) 48703212' }}</div>
            </div>

            <img src="{{ asset('images/logo_man20.png') }}" alt="Logo MAN 20" class="kop-logo-right">
        </div>

        <!-- DOCUMENT TITLE -->
        <div class="doc-title-box">
            <h1 class="doc-title">JADWAL MENGAJAR GURU</h1>
            <div class="doc-subtitle">
                SEMESTER {{ strtoupper($settings['semester'] ?? 'GANJIL') }} &bull; TAHUN PELAJARAN {{ $settings['academic_year'] ?? '2026/2027' }}
            </div>
        </div>

        <!-- METADATA BAR -->
        <div class="meta-bar">
            <div class="meta-item">
                <span>NAMA GURU:</span> <strong>{{ $teacher->name }}</strong>
            </div>
            <div class="meta-item">
                <span>NIP:</span> <strong>{{ $teacher->nip ?? '-' }}</strong>
            </div>
            <div class="meta-item">
                <span>TUGAS TAMBAHAN:</span> <strong>{{ $teacher->additional_role ?: 'Guru Pengajar' }}</strong>
            </div>
            <div class="meta-item">
                <span>TOTAL MENGAJAR:</span> <strong>{{ $teacher->total_teaching_hours }} JP/Minggu</strong>
            </div>
        </div>

        @php
            $times = [
                1 => '06:30 - 07:15',
                2 => '07:15 - 08:00',
                3 => '08:00 - 08:45',
                4 => '08:45 - 09:30',
                5 => '09:50 - 10:35',
                6 => '10:35 - 11:20',
                7 => '11:20 - 12:05',
                8 => '12:45 - 13:30',
                9 => '13:30 - 14:15',
                10 => '14:15 - 15:00',
            ];
        @endphp

        <!-- TIMETABLE -->
        <table class="timetable">
            <thead>
                <tr>
                    <th class="day-col">HARI</th>
                    @for($p = 1; $p <= 4; $p++)
                        <th>
                            JAM {{ $p }}
                            <span class="th-time">{{ $times[$p] }}</span>
                        </th>
                    @endfor
                    <th class="th-break">
                        ISTIRAHAT I
                        <span class="th-time">09:30 - 09:50</span>
                    </th>
                    @for($p = 5; $p <= 7; $p++)
                        <th>
                            JAM {{ $p }}
                            <span class="th-time">{{ $times[$p] }}</span>
                        </th>
                    @endfor
                    <th class="th-break">
                        ISTIRAHAT II
                        <span class="th-time">12:05 - 12:45 *</span>
                    </th>
                    @for($p = 8; $p <= 10; $p++)
                        <th>
                            JAM {{ $p }}
                            <span class="th-time">{{ $times[$p] }}</span>
                        </th>
                    @endfor
                    <th class="th-break">
                        SALAT ASHAR
                        <span class="th-time">15:00 - 15:30</span>
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach($days as $index => $day)
                    <tr>
                        <td class="day-col">{{ strtoupper($day) }}</td>
                        
                        <!-- JAM 1 - 4 -->
                        @for($p = 1; $p <= 4; $p++)
                            @php $s = $schedulesGrid[$day][$p] ?? null; @endphp
                            <td>
                                @if($s)
                                    <div class="cell-content">
                                        <div class="subj-name">{{ $s->subject->name }}</div>
                                        <div class="class-badge">Kelas {{ $s->classroom->name }}</div>
                                    </div>
                                @else
                                    <span style="color: #cbd5e1;">-</span>
                                @endif
                            </td>
                        @endfor

                        <!-- BREAK 1 (Spanning all 5 days) -->
                        @if($index === 0)
                            <td rowspan="5" class="td-break">
                                <div class="break-box">
                                    <div class="break-tag">ISTIRAHAT I</div>
                                    <div class="break-title">SALAT DUHA</div>
                                    <div class="break-time-sub">09.30 - 09.50</div>
                                </div>
                            </td>
                        @endif

                        <!-- JAM 5 - 7 -->
                        @for($p = 5; $p <= 7; $p++)
                            @php $s = $schedulesGrid[$day][$p] ?? null; @endphp
                            <td>
                                @if($s)
                                    <div class="cell-content">
                                        <div class="subj-name">{{ $s->subject->name }}</div>
                                        <div class="class-badge">Kelas {{ $s->classroom->name }}</div>
                                    </div>
                                @else
                                    <span style="color: #cbd5e1;">-</span>
                                @endif
                            </td>
                        @endfor

                        <!-- BREAK 2 (Spanning all 5 days) -->
                        @if($index === 0)
                            <td rowspan="5" class="td-break">
                                <div class="break-box">
                                    <div class="break-tag">ISTIRAHAT II</div>
                                    <div class="break-title">SALAT ZUHUR / JUMAT</div>
                                    <div class="break-time-sub">12.05 - 12.45 *</div>
                                </div>
                            </td>
                        @endif

                        <!-- JAM 8 - 10 -->
                        @for($p = 8; $p <= 10; $p++)
                            @php $s = $schedulesGrid[$day][$p] ?? null; @endphp
                            <td>
                                @if($s)
                                    <div class="cell-content">
                                        <div class="subj-name">{{ $s->subject->name }}</div>
                                        <div class="class-badge">Kelas {{ $s->classroom->name }}</div>
                                    </div>
                                @else
                                    <span style="color: #cbd5e1;">-</span>
                                @endif
                            </td>
                        @endfor

                        <!-- SALAT ASHAR (Spanning all 5 days) -->
                        @if($index === 0)
                            <td rowspan="5" class="td-break">
                                <div class="break-box">
                                    <div class="break-tag">IBADAH</div>
                                    <div class="break-title">SALAT ASHAR</div>
                                    <div class="break-time-sub">15.00 - 15.30</div>
                                </div>
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="schedule-note-bar">
            <span>* <em>Catatan Khusus Hari Jumat:</em> Istirahat II & Salat Jumat pukul 11:20 - 12:30 WIB, Jam ke-7 pukul 12:30 - 13:15 WIB, dan Salat Ashar pukul 15:30 - 16:00 WIB.</span>
            <span>Setelah salat Ashar piket kelas wajib membersihkan kelasnya sebelum pulang.</span>
        </div>

        <!-- SIGNATURE SECTION -->
        <div class="sign-container">
            <!-- Left: Guru Pengampu -->
            <div class="sign-box">
                <div class="role-title">
                    Guru Pengampu,<br>&nbsp;
                </div>
                
                <div class="sign-image-wrapper">
                    @if(!empty($teacher->signature_image) && file_exists(public_path($teacher->signature_image)))
                        <img src="{{ asset($teacher->signature_image) }}" alt="TTD Guru" class="sign-img">
                    @else
                        <!-- Blank space for physical sign -->
                    @endif
                </div>

                <div class="person-name">{{ $teacher->name }}</div>
                <div class="person-nip">NIP. {{ $teacher->nip ?? '-' }}</div>
            </div>

            <!-- Right: Kepala Madrasah -->
            <div class="sign-box">
                <div class="role-title">
                    Jakarta, 13 Juli {{ date('Y') }}<br>
                    Kepala Madrasah
                </div>
                
                <div class="sign-image-wrapper">
                    @if(!empty($settings['school_stamp']) && file_exists(public_path($settings['school_stamp'])))
                        <img src="{{ asset($settings['school_stamp']) }}" alt="Stempel Madrasah" class="stamp-img">
                    @endif
                    @if(!empty($settings['principal_signature']) && file_exists(public_path($settings['principal_signature'])))
                        <img src="{{ asset($settings['principal_signature']) }}" alt="TTD Kepala Madrasah" class="sign-img">
                    @endif
                </div>

                <div class="person-name">{{ $settings['principal_name'] ?? 'Drs. SODIKIN, M.Si.' }}</div>
                <div class="person-nip">NIP. {{ $settings['principal_nip'] ?? '196805101994031002' }}</div>
            </div>
        </div>

    </div>

</body>
</html>
