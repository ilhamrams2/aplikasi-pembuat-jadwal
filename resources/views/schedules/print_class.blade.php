<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Jadwal Pelajaran Kelas {{ $classroom->name }} - MAN 20 Jakarta Timur</title>
    <style>
        @page { size: A4 landscape; margin: 12mm; }
        body { font-family: Arial, sans-serif; font-size: 11px; color: #111; margin: 0; padding: 10px; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 8px; margin-bottom: 15px; }
        .header h2 { margin: 0; font-size: 16px; font-weight: bold; text-transform: uppercase; }
        .header h3 { margin: 3px 0; font-size: 13px; font-weight: normal; }
        .meta { display: flex; justify-content: space-between; margin-bottom: 12px; font-weight: bold; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #333; padding: 6px 4px; text-align: center; }
        th { background-color: #f1f5f9; font-weight: bold; font-size: 10px; }
        .subject-name { font-weight: bold; font-size: 10px; }
        .teacher-name { font-size: 8.5px; color: #333; margin-top: 2px; }
        .footer { margin-top: 25px; display: flex; justify-content: space-between; text-align: center; font-size: 11px; }
        .sign-box { width: 220px; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 15px; text-align: right;">
        <button onclick="window.print()" style="padding: 8px 16px; background: #2563eb; color: #fff; border: none; border-radius: 6px; cursor: pointer; font-weight: bold;">
            Cetak Dokumen (Print / PDF)
        </button>
    </div>

    <div class="header">
        <h2>{{ $settings['school_name'] ?? 'KEMENTERIAN AGAMA RI - MAN 20 JAKARTA TIMUR' }}</h2>
        <h3>JADWAL PELAJARAN SEMESTER {{ strtoupper($settings['semester'] ?? 'GANJIL') }} - TAHUN PELAJARAN {{ $settings['academic_year'] ?? '2026/2027' }}</h3>
    </div>

    <div class="meta">
        <div>KELAS / ROMBEL: <u>{{ $classroom->name }}</u></div>
        <div>WALI KELAS: <u>{{ $classroom->homeroomTeacher->name ?? '-' }}</u></div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 50px;">HARI</th>
                @foreach($periods as $p)
                    <th>
                        JAM {{ $p }}<br>
                        <span style="font-weight: normal; font-size: 8px;">
                            {{ $timeSlots[$p]->start_time ?? '' }}-{{ $timeSlots[$p]->end_time ?? '' }}
                        </span>
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($days as $day)
                <tr>
                    <td style="font-weight: bold; background: #f8fafc;">{{ strtoupper($day) }}</td>
                    @foreach($periods as $p)
                        @php $s = $schedulesGrid[$day][$p] ?? null; @endphp
                        <td>
                            @if($s)
                                <div class="subject-name">{{ $s->subject->name }}</div>
                                <div class="teacher-name">{{ $s->teacher->name }}</div>
                            @else
                                -
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <div class="sign-box">
            Mengetahui,<br>
            Wali Kelas {{ $classroom->name }}<br><br><br><br>
            <strong><u>{{ $classroom->homeroomTeacher->name ?? '( ........................................ )' }}</u></strong><br>
            NIP. {{ $classroom->homeroomTeacher->nip ?? '-' }}
        </div>
        <div class="sign-box">
            Jakarta, {{ date('d F Y') }}<br>
            Kepala Madrasah<br><br><br><br>
            <strong><u>{{ $settings['principal_name'] ?? 'Drs. SODIKIN, M.Si.' }}</u></strong><br>
            NIP. {{ $settings['principal_nip'] ?? '196805101994031002' }}
        </div>
    </div>
</body>
</html>
