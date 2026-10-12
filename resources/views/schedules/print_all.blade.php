<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Master Jadwal Pelajaran MAN 20 Jakarta Timur</title>
    <style>
        @page { size: A3 landscape; margin: 10mm; }
        body { font-family: Arial, sans-serif; font-size: 9px; color: #111; margin: 0; padding: 10px; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 8px; margin-bottom: 12px; }
        .header h2 { margin: 0; font-size: 15px; font-weight: bold; text-transform: uppercase; }
        .header h3 { margin: 3px 0; font-size: 11px; font-weight: normal; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { border: 1px solid #444; padding: 4px 2px; text-align: center; }
        th { background-color: #f1f5f9; font-weight: bold; font-size: 9px; }
        .day-header { background: #e2e8f0; font-weight: bold; font-size: 10px; text-align: left; padding-left: 8px; }
        .sub { font-weight: bold; font-size: 8.5px; }
        .tch { font-size: 7.5px; color: #444; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 15px; text-align: right;">
        <button onclick="window.print()" style="padding: 8px 16px; background: #2563eb; color: #fff; border: none; border-radius: 6px; cursor: pointer; font-weight: bold;">
            Cetak Master (Print / PDF)
        </button>
    </div>

    <div class="header">
        <h2>{{ $settings['school_name'] ?? 'MAN 20 JAKARTA TIMUR' }}</h2>
        <h3>MASTER JADWAL PELAJARAN LENGKAP - TAHUN PELAJARAN {{ $settings['academic_year'] ?? '2026/2027' }}</h3>
    </div>

    @foreach($days as $day)
        <h4 style="margin: 8px 0 4px; font-size: 11px; text-transform: uppercase; background: #0f172a; color: #fff; padding: 4px 8px; border-radius: 4px;">
            HARI {{ $day }}
        </h4>
        <table>
            <thead>
                <tr>
                    <th style="width: 70px;">KELAS</th>
                    @foreach($periods as $p)
                        <th>JAM {{ $p }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($classrooms as $cls)
                    <tr>
                        <td style="font-weight: bold; background: #f8fafc;">Kelas {{ $cls->name }}</td>
                        @foreach($periods as $p)
                            @php $item = $matrix[$day][$cls->id][$p] ?? null; @endphp
                            <td>
                                @if($item)
                                    <div class="sub">{{ $item->subject->name }}</div>
                                    <div class="tch">{{ Str::limit($item->teacher->name, 18) }}</div>
                                @else
                                    -
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach

    <div style="margin-top: 20px; text-align: right; font-size: 10px;">
        Jakarta, {{ date('d F Y') }}<br>
        Kepala Madrasah,<br><br><br><br>
        <strong><u>{{ $settings['principal_name'] ?? 'Drs. SODIKIN, M.Si.' }}</u></strong><br>
        NIP. {{ $settings['principal_nip'] ?? '196805101994031002' }}
    </div>
</body>
</html>
