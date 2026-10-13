<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Pelajaran Resmi MAN 20 Jakarta Timur - A4 Portrait</title>
    
    <!-- Google Fonts & Font Awesome -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800;900&family=Arial:wght@400;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- html2pdf.js for Direct 1-Click PDF Download without print dialog -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <style>
        /* Exact A4 Portrait Dimensions (210mm x 297mm) - Strictly 1 Page Only */
        @page {
            size: A4 portrait;
            margin: 3.5mm 4mm;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        html, body {
            margin: 0;
            padding: 0;
            background: #cbd5e1;
            color: #000;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 6.8px;
            line-height: 1.05;
            -webkit-font-smoothing: antialiased;
        }

        /* Screen Wrapper */
        .screen-container {
            max-width: 900px;
            margin: 12px auto;
            padding: 0 10px;
        }

        /* Toolbar Top (Screen Only) */
        .no-print {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #0f172a;
            color: #fff;
            padding: 10px 18px;
            border-radius: 12px;
            margin-bottom: 12px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.25);
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
        }
        .btn-action {
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 800;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            transition: all 0.2s ease;
            border: none;
        }
        .btn-download-pdf {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: #fff;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.35);
        }
        .btn-download-pdf:hover {
            background: linear-gradient(135deg, #1d4ed8, #1e40af);
            transform: translateY(-1px);
        }
        .btn-print-dialog {
            background: #334155;
            color: #cbd5e1;
            border: 1px solid #475569;
        }
        .btn-print-dialog:hover {
            background: #475569;
            color: #fff;
        }
        .btn-back {
            background: #1e293b;
            color: #94a3b8;
            border: 1px solid #334155;
        }
        .btn-back:hover {
            background: #334155;
            color: #fff;
        }

        /* A4 Page Canvas (Exact Dimensions: 210mm x 297mm Portrait) */
        .a4-page-canvas {
            width: 210mm;
            height: 295mm;
            max-height: 295mm;
            margin: 0 auto;
            background: #fff;
            padding: 3.5mm 4mm;
            box-sizing: border-box;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            page-break-inside: avoid;
            page-break-after: avoid;
            break-inside: avoid;
        }

        /* HEADER SECTION */
        .doc-header {
            position: relative;
            text-align: center;
            margin-bottom: 2px;
            padding: 0;
            height: 42px;
        }
        .logo-left {
            position: absolute;
            left: 4px;
            top: 1px;
            height: 38px;
            width: auto;
        }
        .logo-right {
            position: absolute;
            right: 4px;
            top: 1px;
            height: 38px;
            width: auto;
        }
        .doc-header h1 {
            font-size: 11.5px;
            font-weight: 900;
            margin: 0;
            letter-spacing: 0.3px;
            text-transform: uppercase;
            line-height: 1.15;
        }
        .doc-header h2 {
            font-size: 12.5px;
            font-weight: 900;
            margin: 1px 0;
            letter-spacing: 0.3px;
            text-transform: uppercase;
            line-height: 1.15;
        }
        .doc-header h3 {
            font-size: 10.5px;
            font-weight: 800;
            margin: 0;
            letter-spacing: 0.3px;
            line-height: 1.15;
        }
        .revision-bar {
            font-size: 7.2px;
            font-weight: 900;
            text-transform: uppercase;
            margin-bottom: 3px;
            padding-left: 2px;
            line-height: 1.1;
            border-bottom: 1px solid #000;
            padding-bottom: 2px;
        }

        /* MAIN CONTAINER (2 COLUMNS SIDE-BY-SIDE) */
        .main-container {
            display: flex;
            gap: 4px;
            align-items: flex-start;
            width: 100%;
            height: calc(100% - 50px);
            overflow: hidden;
        }
        .left-col {
            flex: 1 1 54%;
            max-width: 54%;
            height: 100%;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        .right-col {
            flex: 1 1 46%;
            max-width: 46%;
            height: 100%;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        /* TABLE BASE STYLES */
        table {
            border-collapse: collapse;
            width: 100%;
            border: 1px solid #000;
        }
        th, td {
            border: 1px solid #000;
            padding: 0.6px 0.4px;
            text-align: center;
            vertical-align: middle;
            font-size: 6.8px;
            line-height: 1.05;
        }
        th {
            font-weight: 900;
            background-color: #fff;
        }

        /* SCHEDULE TABLES (LEFT COLUMN) */
        .day-table {
            margin-bottom: 2px;
            border-collapse: collapse;
            width: 100%;
        }
        .day-table th {
            font-size: 6.5px;
            font-weight: 900;
            padding: 0.8px 0.4px;
            background-color: #fff;
        }
        .day-table td {
            height: 10.4px;
            font-size: 6.8px;
            padding: 0.4px 0.3px;
        }
        .day-label {
            font-weight: 900;
            font-size: 9px;
            letter-spacing: 0.5px;
            line-height: 1;
            text-transform: uppercase;
            background-color: #fff;
            width: 12px;
            padding: 0 !important;
        }
        .break-cell {
            font-weight: 900;
            font-size: 6.8px;
            letter-spacing: 0.3px;
            text-transform: uppercase;
            background-color: #fff;
            padding: 0.4px !important;
        }
        .piket-cell {
            font-weight: 800;
            font-size: 6px;
            text-align: left;
            padding-left: 4px !important;
            background-color: #fff;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .piket-label {
            font-weight: 900;
            font-size: 6.6px;
            text-align: center;
            background-color: #fff;
        }
        .cell-pramuka {
            font-weight: 900;
            font-size: 8px;
            letter-spacing: 0.5px;
            background-color: #fff;
        }
        .cell-code {
            font-weight: bold;
            font-size: 6.8px;
        }

        /* RIGHT COLUMN TABLES (TEACHERS & WALAS) */
        .tbl-side th {
            font-size: 6.4px;
            font-weight: 900;
            padding: 0.7px 0.4px;
            background-color: #fff;
        }
        .tbl-side td {
            font-size: 6.5px;
            padding: 0.5px 0.8px;
            height: 9.6px;
            line-height: 1;
        }
        .bg-green-total {
            background-color: #86efac !important;
            font-weight: 900;
        }
        .tbl-wali-header {
            background-color: #fed7aa !important;
            font-weight: 900;
        }

        /* SIGNATURES SECTION */
        .signature-section {
            margin-top: 5px;
            display: flex;
            justify-content: space-between;
            font-size: 6.8px;
            line-height: 1.15;
            padding: 0 6px;
        }
        .sig-box {
            text-align: center;
            width: 48%;
        }
        .sig-img {
            height: 28px;
            max-width: 100px;
            object-fit: contain;
            margin: 1px auto;
            display: block;
        }

        .footer-note {
            font-size: 6.6px;
            margin-top: 2px;
            line-height: 1;
        }

        /* PRINT STYLES - STRICTLY A4 PORTRAIT 1 PAGE */
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: #fff !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .screen-container {
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            .a4-page-canvas {
                width: 100% !important;
                height: 100% !important;
                max-height: 295mm !important;
                box-shadow: none !important;
                padding: 3mm 4mm !important;
            }
        }
    </style>
</head>
<body>

    <div class="screen-container">

        <!-- ACTION TOOLBAR (SCREEN ONLY) -->
        <div class="no-print">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 38px; height: 38px; border-radius: 10px; background: linear-gradient(135deg, #2563eb, #4f46e5); display: flex; align-items: center; justify-content: center; font-size: 16px;">
                    <i class="fa-solid fa-file-pdf"></i>
                </div>
                <div>
                    <div style="font-size: 14px; font-weight: 800; display: flex; align-items: center; gap: 8px;">
                        Jadwal Pelajaran MAN 20 Jakarta
                        <span style="font-size: 10px; font-weight: 700; background: #3b82f6; color: #fff; padding: 2px 8px; border-radius: 9999px;">
                            A4 Portrait (1 Halaman)
                        </span>
                    </div>
                    <div style="font-size: 11px; color: #94a3b8;">
                        Format lengkap &bull; Data jadwal, guru, walas, dan guru piket terhubung dinamis
                    </div>
                </div>
            </div>

            <div style="display: flex; gap: 8px; align-items: center;">
                <a href="{{ route('schedules.index') }}" class="btn-action btn-back">
                    <i class="fa-solid fa-arrow-left"></i> Kembali
                </a>
                <a href="{{ route('pikets.index') }}" class="btn-action btn-print-dialog" title="Kelola Jadwal Guru Piket">
                    <i class="fa-solid fa-clipboard-user"></i> Ubah Guru Piket
                </a>
                <a href="{{ route('schedules.export.excel') }}" class="btn-action" style="background: #10b981; color: white; display: inline-flex; align-items: center; gap: 6px; padding: 7px 14px; border-radius: 8px; font-size: 11px; font-weight: 700; text-decoration: none; border: none; cursor: pointer; transition: 0.15s;" title="Cetak / Download Format Excel">
                    <i class="fa-solid fa-file-excel"></i> Cetak Excel
                </a>
                <button onclick="window.print()" class="btn-action btn-download-pdf" title="Cetak Dokumen">
                    <i class="fa-solid fa-print"></i> Cetak
                </button>
            </div>
        </div>

        <!-- PRINTABLE CANVAS (STRICTLY EXACT A4 PORTRAIT 1 PAGE ONLY) -->
        <div id="printableArea" class="a4-page-canvas">

            <!-- DOCUMENT HEADER -->
            <div class="doc-header">
                <img src="{{ asset('images/logo_kemenag.png') }}" alt="Kemenag" class="logo-left">
                <img src="{{ asset('images/logo_man20.png') }}" alt="MAN 20" class="logo-right">
                <h1>{{ $header['title'] ?? 'JADWAL PELAJARAN SEMESTER GANJIL' }}</h1>
                <h2>{{ $header['school_name'] ?? 'MAN 20 JAKARTA TIMUR' }}</h2>
                <h3>{{ $header['academic_year'] ?? 'TAHUN PELAJARAN 2026-2027' }}</h3>
            </div>

            <div class="revision-bar">
                {{ $header['revision'] ?? 'REVISI4_BERLAKU MULAI : Senin, 12 Oktober 2026' }}
            </div>

            <!-- MAIN TWO COLUMN CONTAINER -->
            <div class="main-container">

                <!-- LEFT COLUMN: MASTER TIMETABLE PER HARI (SENIN, SELASA, RABU, KAMIS, JUMAT) -->
                <div class="left-col">
                    @php
                        $dayLabels = [
                            'Senin' => "S<br>E<br>N<br>I<br>N",
                            'Selasa' => "S<br>E<br>L<br>A<br>S<br>A",
                            'Rabu' => "R<br>A<br>B<br>U",
                            'Kamis' => "K<br>A<br>M<br>I<br>S",
                            'Jumat' => "J<br>U<br>M<br>-<br>'<br>A<br>T",
                        ];
                        $totalClassCount = $classrooms->count();
                        $grade10Count = $classrooms->where('grade', 10)->count();
                    @endphp

                    @foreach($daysSchedule as $dayName => $dayInfo)
                        <table class="day-table">
                            <thead>
                                <tr>
                                    <th rowspan="2" style="width: 13px;">Hari</th>
                                    <th rowspan="2" style="width: 13px;">Jam<br>Ke</th>
                                    <th rowspan="2" style="width: 46px;">Waktu</th>
                                    <th colspan="{{ $grade10Count }}">KELAS X</th>
                                    <th colspan="{{ $classrooms->where('grade', 11)->count() }}">KELAS XI</th>
                                    <th colspan="{{ $classrooms->where('grade', 12)->count() }}">KELAS XII</th>
                                </tr>
                                <tr>
                                    @foreach($classrooms as $cls)
                                        <th style="width: calc((100% - 72px) / {{ $totalClassCount }});">
                                            {{ str_replace('.', '-', $cls->name) }}
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totalRows = 14;
                                @endphp

                                <!-- ROW 1 (Jam 1) -->
                                <tr>
                                    <td rowspan="{{ $totalRows }}" class="day-label">{!! $dayLabels[$dayName] !!}</td>
                                    <td>1</td>
                                    <td>{{ $dayInfo['periods'][1]['time'] }}</td>

                                    @if($dayName === 'Rabu' && isset($dayInfo['periods'][1]['special_merge_x']))
                                        <!-- RABU JAM 1: PRAMUKA MERGED ROWSPAN 2 UNTUK KELAS X -->
                                        <td rowspan="2" colspan="{{ $grade10Count }}" class="cell-pramuka">PRAMUKA</td>
                                        @foreach($classrooms->where('grade', '!=', 10) as $cls)
                                            <td class="cell-code">{{ $dayInfo['periods'][1]['slots'][$cls->id] ?? '' }}</td>
                                        @endforeach
                                    @else
                                        @foreach($classrooms as $cls)
                                            <td class="cell-code">{{ $dayInfo['periods'][1]['slots'][$cls->id] ?? '' }}</td>
                                        @endforeach
                                    @endif
                                </tr>

                                <!-- ROW 2 (Jam 2) -->
                                <tr>
                                    <td>2</td>
                                    <td>{{ $dayInfo['periods'][2]['time'] }}</td>
                                    @if($dayName === 'Rabu' && isset($dayInfo['periods'][2]['special_merge_x']))
                                        @foreach($classrooms->where('grade', '!=', 10) as $cls)
                                            <td class="cell-code">{{ $dayInfo['periods'][2]['slots'][$cls->id] ?? '' }}</td>
                                        @endforeach
                                    @else
                                        @foreach($classrooms as $cls)
                                            <td class="cell-code">{{ $dayInfo['periods'][2]['slots'][$cls->id] ?? '' }}</td>
                                        @endforeach
                                    @endif
                                </tr>

                                <!-- ROW 3 (Jam 3) -->
                                <tr>
                                    <td>3</td>
                                    <td>{{ $dayInfo['periods'][3]['time'] }}</td>
                                    @foreach($classrooms as $cls)
                                        <td class="cell-code">{{ $dayInfo['periods'][3]['slots'][$cls->id] ?? '' }}</td>
                                    @endforeach
                                </tr>

                                <!-- ROW 4 (Jam 4) -->
                                <tr>
                                    <td>4</td>
                                    <td>{{ $dayInfo['periods'][4]['time'] }}</td>
                                    @foreach($classrooms as $cls)
                                        <td class="cell-code">{{ $dayInfo['periods'][4]['slots'][$cls->id] ?? '' }}</td>
                                    @endforeach
                                </tr>

                                <!-- BREAK 1: ISTIRAHAT / SALAT DUHA -->
                                <tr>
                                    <td></td>
                                    <td>{{ $dayInfo['periods']['break_1']['time'] }}</td>
                                    <td colspan="{{ $totalClassCount }}" class="break-cell">{{ $dayInfo['periods']['break_1']['label'] }}</td>
                                </tr>

                                <!-- ROW 5 (Jam 5) -->
                                <tr>
                                    <td>5</td>
                                    <td>{{ $dayInfo['periods'][5]['time'] }}</td>
                                    @foreach($classrooms as $cls)
                                        <td class="cell-code">{{ $dayInfo['periods'][5]['slots'][$cls->id] ?? '' }}</td>
                                    @endforeach
                                </tr>

                                <!-- ROW 6 (Jam 6) -->
                                <tr>
                                    <td>6</td>
                                    <td>{{ $dayInfo['periods'][6]['time'] }}</td>
                                    @foreach($classrooms as $cls)
                                        <td class="cell-code">{{ $dayInfo['periods'][6]['slots'][$cls->id] ?? '' }}</td>
                                    @endforeach
                                </tr>

                                @if($dayName !== 'Jumat')
                                    <!-- ROW 7 (Jam 7) for Senin - Kamis -->
                                    <tr>
                                        <td>7</td>
                                        <td>{{ $dayInfo['periods'][7]['time'] }}</td>
                                        @foreach($classrooms as $cls)
                                            <td class="cell-code">{{ $dayInfo['periods'][7]['slots'][$cls->id] ?? '' }}</td>
                                        @endforeach
                                    </tr>

                                    <!-- BREAK 2: ISTIRAHAT DAN SALAT ZUHUR BERJAMAAH -->
                                    <tr>
                                        <td></td>
                                        <td>{{ $dayInfo['periods']['break_2']['time'] }}</td>
                                        <td colspan="{{ $totalClassCount }}" class="break-cell">{{ $dayInfo['periods']['break_2']['label'] }}</td>
                                    </tr>
                                @else
                                    <!-- BREAK 2 (JUMAT): ISTIRAHAT DAN SALAT JUMAT/ZUHUR -->
                                    <tr>
                                        <td></td>
                                        <td>{{ $dayInfo['periods']['break_2']['time'] }}</td>
                                        <td colspan="{{ $totalClassCount }}" class="break-cell">{{ $dayInfo['periods']['break_2']['label'] }}</td>
                                    </tr>

                                    <!-- ROW 7 (Jam 7) for Jumat -->
                                    <tr>
                                        <td>7</td>
                                        <td>{{ $dayInfo['periods'][7]['time'] }}</td>
                                        @foreach($classrooms as $cls)
                                            <td class="cell-code">{{ $dayInfo['periods'][7]['slots'][$cls->id] ?? '' }}</td>
                                        @endforeach
                                    </tr>
                                @endif

                                <!-- ROW 8 (Jam 8) -->
                                <tr>
                                    <td>8</td>
                                    <td>{{ $dayInfo['periods'][8]['time'] }}</td>
                                    @foreach($classrooms as $cls)
                                        <td class="cell-code">{{ $dayInfo['periods'][8]['slots'][$cls->id] ?? '' }}</td>
                                    @endforeach
                                </tr>

                                <!-- ROW 9 (Jam 9) -->
                                <tr>
                                    <td>9</td>
                                    <td>{{ $dayInfo['periods'][9]['time'] }}</td>
                                    @foreach($classrooms as $cls)
                                        <td class="cell-code">{{ $dayInfo['periods'][9]['slots'][$cls->id] ?? '' }}</td>
                                    @endforeach
                                </tr>

                                <!-- ROW 10 (Jam 10) -->
                                <tr>
                                    <td>10</td>
                                    <td>{{ $dayInfo['periods'][10]['time'] }}</td>
                                    @foreach($classrooms as $cls)
                                        <td class="cell-code">{{ $dayInfo['periods'][10]['slots'][$cls->id] ?? '' }}</td>
                                    @endforeach
                                </tr>

                                <!-- SALAT ASHAR -->
                                <tr>
                                    <td colspan="2" class="break-cell" style="font-size: 6.2px; font-weight: bold;">{{ $dayInfo['periods']['salat_ashar']['time'] }}</td>
                                    <td colspan="{{ $totalClassCount }}" class="break-cell">{{ $dayInfo['periods']['salat_ashar']['label'] }}</td>
                                </tr>

                                <!-- PIKET ROW -->
                                <tr>
                                    <td colspan="2" class="piket-label">PIKET</td>
                                    <td colspan="{{ $totalClassCount }}" class="piket-cell">{{ $dayInfo['piket'] }}</td>
                                </tr>

                            </tbody>
                        </table>
                    @endforeach

                    <div class="footer-note">
                        {{ $header['note'] ?? 'Catatan: Setelah salat Ashar piket kelas wajib membersihkan kelasnya sebelum pulang' }}
                    </div>
                </div>

                <!-- RIGHT COLUMN: TEACHERS & WALI KELAS & SIGNATURES -->
                <div class="right-col">

                    <!-- TABLE 1: DAFTAR GURU & BEBAN MENGAJAR (DINAMIS DARI DATABASE) -->
                    <table class="tbl-side">
                        <thead>
                            <tr>
                                <th rowspan="2" style="width: 14px;">Kode<br>Guru</th>
                                <th rowspan="2" style="width: 110px; text-align: left; padding-left: 3px;">NAMA GURU</th>
                                <th rowspan="2" style="text-align: left; padding-left: 3px;">Mata Pelajaran</th>
                                <th colspan="5">Hari Mengajar</th>
                                <th rowspan="2" style="width: 14px;" class="bg-green-total">JML<br>Jam</th>
                            </tr>
                            <tr>
                                <th style="width: 7px;">S</th>
                                <th style="width: 7px;">S</th>
                                <th style="width: 7px;">R</th>
                                <th style="width: 7px;">K</th>
                                <th style="width: 7px;">J</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($teachersList as $code => $t)
                                <tr>
                                    <td style="font-weight: bold;">{{ ltrim($code, '0') ?: '0' }}</td>
                                    <td style="text-align: left; padding-left: 2px; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 110px;">
                                        {{ $t['name'] }}
                                    </td>
                                    <td style="text-align: left; padding-left: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 130px;" title="{{ $t['subject'] }}">
                                        {{ $t['subject'] }}
                                    </td>
                                    <td>{{ $t['days'][0] }}</td>
                                    <td>{{ $t['days'][1] }}</td>
                                    <td>{{ $t['days'][2] }}</td>
                                    <td>{{ $t['days'][3] }}</td>
                                    <td>{{ $t['days'][4] }}</td>
                                    <td class="bg-green-total">{{ $t['total'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- TABLE 2: DAFTAR WALI KELAS (DINAMIS DARI RELASI ROMBEL) -->
                    <table class="tbl-side" style="margin-top: 3px;">
                        <thead>
                            <tr class="tbl-wali-header">
                                <th style="width: 16px;">No</th>
                                <th style="text-align: left; padding-left: 4px;">NAMA GURU</th>
                                <th style="width: 60px;">WALI KELAS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($homeroomTeachers as $idx => $wk)
                                <tr>
                                    <td style="font-weight: bold;">{{ $idx }}</td>
                                    <td style="text-align: left; padding-left: 4px; font-weight: 600;">{{ $wk['name'] }}</td>
                                    <td style="font-weight: 800;">{{ str_replace('Kelas ', '', str_replace('.', '-', $wk['class'])) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- SIGNATURES BLOCK -->
                    <div class="signature-section">
                        <div class="sig-box">
                            <div>Mengetahui,</div>
                            <div style="font-weight: bold;">{{ $header['principal_title'] ?? 'Kepala MAN 20 Jakarta' }}</div>
                            <div style="height: 48px; display: flex; align-items: center; justify-content: center; position: relative;">
                                @if(!empty($header['school_stamp']) && file_exists(public_path($header['school_stamp'])))
                                    <img src="{{ asset($header['school_stamp']) }}" alt="Stempel" style="max-height: 45px; position: absolute; left: 10px; opacity: 0.85; mix-blend-mode: multiply;">
                                @endif
                                @if(!empty($header['principal_signature']) && file_exists(public_path($header['principal_signature'])))
                                    <img src="{{ asset($header['principal_signature']) }}" alt="Tanda Tangan Kepala" class="sig-img" style="position: relative; z-index: 2;">
                                @endif
                            </div>
                            <div><strong><u>{{ $header['principal_name'] ?? 'Drs. Sodikin, M.Si' }}</u></strong></div>
                            <div>NIP. {{ $header['principal_nip'] ?? '196810212003121001' }}</div>
                        </div>

                        <div class="sig-box">
                            <div>{{ $header['date_signed'] ?? 'Jakarta, 13 Juli 2026' }}</div>
                            <div style="font-weight: bold;">{{ $header['academic_head_title'] ?? 'Wakil Kepala Bidang Akademik' }}</div>
                            <div style="height: 48px; display: flex; align-items: center; justify-content: center;">
                                @if(!empty($header['academic_head_signature']) && file_exists(public_path($header['academic_head_signature'])))
                                    <img src="{{ asset($header['academic_head_signature']) }}" alt="Tanda Tangan Waka" class="sig-img">
                                @endif
                            </div>
                            <div><strong><u>{{ $header['academic_head_name'] ?? 'Temy Yulianti, S.Pd., M.Pd' }}</u></strong></div>
                            <div>NIP. {{ $header['academic_head_nip'] ?? '197712102009122001' }}</div>
                        </div>
                    </div>

                </div>

            </div>

        </div>

    </div>

    <!-- Direct 1-Click PDF Download Script -->
    <script>
        function downloadPDF() {
            const element = document.getElementById('printableArea');
            const btn = document.getElementById('btnDownloadPdf');
            const originalContent = btn.innerHTML;

            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menyiapkan PDF A4...';
            btn.disabled = true;

            const opt = {
                margin: [3, 3, 3, 3], // mm [top, left, bottom, right]
                filename: 'Jadwal_Pelajaran_Resmi_MAN20_Jakarta_A4.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: {
                    scale: 2.2,
                    useCORS: true,
                    logging: false,
                    letterRendering: true
                },
                jsPDF: {
                    unit: 'mm',
                    format: 'a4',
                    orientation: 'portrait',
                    compress: true
                },
                pagebreak: {
                    mode: ['avoid-all', 'css', 'legacy']
                }
            };

            html2pdf().set(opt).from(element).save().then(() => {
                btn.innerHTML = '<i class="fa-solid fa-check"></i> Berhasil Diunduh!';
                setTimeout(() => {
                    btn.innerHTML = originalContent;
                    btn.disabled = false;
                }, 2500);
            }).catch(err => {
                console.error('PDF error:', err);
                btn.innerHTML = originalContent;
                btn.disabled = false;
                alert('Gagal mengunduh PDF secara otomatis. Silakan gunakan tombol Cetak Printer lalu pilih Simpan sebagai PDF.');
            });
        }

        // Automatic Download if accessed with ?download=1 or ?download=pdf
        window.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('download') === '1' || urlParams.get('download') === 'pdf') {
                setTimeout(downloadPDF, 800);
            }
        });

        function openPiketModal() {
            document.getElementById('piketModal').style.display = 'flex';
        }
        function closePiketModal() {
            document.getElementById('piketModal').style.display = 'none';
        }
    </script>

    <!-- MODAL ATUR GURU PIKET (SCREEN ONLY) -->
    <div id="piketModal" class="no-print" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.7); backdrop-filter: blur(4px); align-items: center; justify-content: center; z-index: 9999; padding: 16px;">
        <div style="background: #fff; border-radius: 20px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); max-width: 540px; width: 100%; padding: 22px; font-family: 'Plus Jakarta Sans', system-ui, sans-serif; max-height: 90vh; overflow-y: auto;">
            <div style="display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #e2e8f0; padding-bottom: 12px; margin-bottom: 14px;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <div style="width: 36px; height: 36px; border-radius: 10px; background: #e0e7ff; color: #4338ca; display: flex; align-items: center; justify-content: center; font-size: 16px;">
                        <i class="fa-solid fa-clipboard-user"></i>
                    </div>
                    <div>
                        <h3 style="font-size: 15px; font-weight: 800; color: #1e293b; margin: 0;">Ubah Guru Piket Madrasah</h3>
                        <p style="font-size: 11px; color: #64748b; margin: 2px 0 0 0;">Perubahan akan langsung ter-update di jadwal dan dokumen cetak resmi</p>
                    </div>
                </div>
                <button type="button" onclick="closePiketModal()" style="width: 30px; height: 30px; border-radius: 50%; border: none; background: #f1f5f9; color: #64748b; cursor: pointer; font-size: 14px; display: flex; align-items: center; justify-content: center;">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form action="{{ route('schedules.piket.update') }}" method="POST" style="display: flex; flex-direction: column; gap: 10px;">
                @csrf

                @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'] as $day)
                    <div style="background: #f8fafc; padding: 10px 12px; border-radius: 10px; border: 1px solid #e2e8f0;">
                        <label style="display: flex; justify-content: space-between; font-size: 11px; font-weight: 800; color: #334155; margin-bottom: 4px;">
                            <span>Hari {{ $day }}</span>
                            <span style="font-weight: 500; font-size: 10px; color: #94a3b8;">Pisahkan nama dengan koma</span>
                        </label>
                        <input type="text" name="piket_{{ $day }}" value="{{ $pikets[$day] ?? '' }}" style="width: 100%; font-size: 11px; padding: 7px 10px; border-radius: 8px; border: 1px solid #cbd5e1; outline: none; font-weight: 600; color: #0f172a; box-sizing: border-box;" placeholder="Nama-nama guru piket...">
                    </div>
                @endforeach

                <div style="display: flex; justify-content: flex-end; gap: 8px; margin-top: 10px; border-top: 1px solid #e2e8f0; padding-top: 12px;">
                    <button type="button" onclick="closePiketModal()" style="padding: 8px 16px; border-radius: 8px; font-size: 12px; font-weight: 700; background: #f1f5f9; color: #475569; border: none; cursor: pointer;">
                        Batal
                    </button>
                    <button type="submit" style="padding: 8px 18px; border-radius: 8px; font-size: 12px; font-weight: 800; background: #2563eb; color: #fff; border: none; cursor: pointer; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);">
                        <i class="fa-solid fa-floppy-disk mr-1"></i> Simpan Guru Piket
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
