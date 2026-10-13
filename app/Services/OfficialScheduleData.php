<?php

namespace App\Services;

class OfficialScheduleData
{
    public static function getHeaderInfo(): array
    {
        return [
            'title' => 'JADWAL PELAJARAN SEMESTER GANJIL',
            'school_name' => 'MAN 20 JAKARTA TIMUR',
            'revision' => \App\Models\SchoolSetting::get('schedule_revision', 'REVISI4_BERLAKU MULAI : Senin, 12 Oktober 2026'),
            'date_signed' => \App\Models\SchoolSetting::get('date_signed', 'Jakarta, 13 Juli 2026'),
            'principal_name' => 'Drs. Sodikin, M.Si',
            'principal_nip' => '196810212003121001',
            'principal_title' => 'Kepala MAN 20 Jakarta',
            'academic_head_name' => 'Temy Yulianti, S.Pd., M.Pd',
            'academic_head_nip' => '197712102009122001',
            'academic_head_title' => 'Wakil Kepala Bidang Akademik',
            'school_stamp' => \App\Models\SchoolSetting::get('school_stamp'),
            'principal_signature' => \App\Models\SchoolSetting::get('principal_signature'),
            'academic_head_signature' => \App\Models\SchoolSetting::get('academic_head_signature'),
            'note' => 'Catatan: Setelah salat Ashar piket kelas wajib membersihkan kelasnya sebelum pulang',
        ];
    }

    public static function getClassColumns(): array
    {
        return [
            'KELAS X' => ['X-1', 'X-2', 'X-3', 'X-4', 'X-5'],
            'KELAS XI' => ['XI-1', 'XI-2', 'XI-3', 'XI-4', 'XI-5'],
            'KELAS XII' => ['XII-1', 'XII-2', 'XII-3', 'XII-4', 'XII-5'],
        ];
    }

    public static function getDaysSchedule(): array
    {
        $pikets = [
            'Senin' => \App\Models\SchoolSetting::get('piket_Senin', 'FIRMANSYAH S.Ag., HANNY MILLENIA S.Pd., M. IKHLAS, S.Pd.'),
            'Selasa' => \App\Models\SchoolSetting::get('piket_Selasa', 'MIFTAH EL FALAH, SE., NURHASANAH S.Pd., LEYLIA ARUM AMALUN NISA S.Pd.'),
            'Rabu' => \App\Models\SchoolSetting::get('piket_Rabu', 'NANANG KOSIM S.Pd., SITI HASANAH S.Ag., IIS SURYANI S.Pd.I.'),
            'Kamis' => \App\Models\SchoolSetting::get('piket_Kamis', 'ALI SUBHAN S.Ag., SITI BAROROH S.Pd., MUHAMAD RICO SAPUTRA, S.Pd.'),
            'Jumat' => \App\Models\SchoolSetting::get('piket_Jumat', 'SITI RUWAIDAH, S.Pd., ARIEL BONARDO H. LIMBONG S.Pd., CITRA ARIYANTI'),
        ];

        return [
            'Senin' => [
                'piket' => $pikets['Senin'],
                'periods' => [
                    1 => ['time' => '06:30 - 07:15', 'slots' => ['X-1' => '35', 'X-2' => '6', 'X-3' => '21', 'X-4' => '13', 'X-5' => '12', 'XI-1' => '34', 'XI-2' => '14', 'XI-3' => '17T', 'XI-4' => '28', 'XI-5' => '25', 'XII-1' => '7', 'XII-2' => '9', 'XII-3' => '31', 'XII-4' => '16', 'XII-5' => '33S']],
                    2 => ['time' => '07:15 - 08:00', 'slots' => ['X-1' => '35', 'X-2' => '6', 'X-3' => '21', 'X-4' => '13', 'X-5' => '12', 'XI-1' => '34', 'XI-2' => '14', 'XI-3' => '17T', 'XI-4' => '28', 'XI-5' => '25', 'XII-1' => '7', 'XII-2' => '9', 'XII-3' => '31', 'XII-4' => '16', 'XII-5' => '33S']],
                    3 => ['time' => '08:00 - 08:45', 'slots' => ['X-1' => '31', 'X-2' => '6', 'X-3' => '9', 'X-4' => '15E', 'X-5' => '22', 'XI-1' => '33S', 'XI-2' => '13', 'XI-3' => '2', 'XI-4' => '22', 'XI-5' => '25', 'XII-1' => '7', 'XII-2' => '31', 'XII-3' => '22', 'XII-4' => '12', 'XII-5' => '16']],
                    4 => ['time' => '08:45 - 09:30', 'slots' => ['X-1' => '31', 'X-2' => '17', 'X-3' => '9', 'X-4' => '15E', 'X-5' => '22', 'XI-1' => '33S', 'XI-2' => '13', 'XI-3' => '2', 'XI-4' => '22', 'XI-5' => '28', 'XII-1' => '35', 'XII-2' => '31', 'XII-3' => '22', 'XII-4' => '12', 'XII-5' => '16']],
                    'break_1' => ['time' => '09:30 - 09:50', 'label' => 'ISTIRAHAT / SALAT DUHA'],
                    5 => ['time' => '09:50 - 10:35', 'slots' => ['X-1' => '34', 'X-2' => '17', 'X-3' => '35', 'X-4' => '31', 'X-5' => '3', 'XI-1' => '9', 'XI-2' => '19', 'XI-3' => '33S', 'XI-4' => '25', 'XI-5' => '28', 'XII-1' => '6', 'XII-2' => '14', 'XII-3' => '36', 'XII-4' => '23', 'XII-5' => '27']],
                    6 => ['time' => '10:35 - 11:20', 'slots' => ['X-1' => '34', 'X-2' => '13', 'X-3' => '17', 'X-4' => '31', 'X-5' => '3', 'XI-1' => '7', 'XI-2' => '19', 'XI-3' => '33S', 'XI-4' => '25', 'XI-5' => '12', 'XII-1' => '6', 'XII-2' => '14', 'XII-3' => '36', 'XII-4' => '23', 'XII-5' => '27']],
                    7 => ['time' => '11:20 - 12:05', 'slots' => ['X-1' => '34', 'X-2' => '13', 'X-3' => '17', 'X-4' => '9', 'X-5' => '29', 'XI-1' => '7', 'XI-2' => '33S', 'XI-3' => '8', 'XI-4' => '25', 'XI-5' => '12', 'XII-1' => '6', 'XII-2' => '14', 'XII-3' => '36', 'XII-4' => '23', 'XII-5' => '27']],
                    'break_2' => ['time' => '12:05 - 12:45', 'label' => 'ISTIRAHAT DAN SALAT ZUHUR BERJAMAAH'],
                    8 => ['time' => '12:45 - 13:30', 'slots' => ['X-1' => '29', 'X-2' => '15E', 'X-3' => '6', 'X-4' => '9', 'X-5' => '4', 'XI-1' => '5', 'XI-2' => '33S', 'XI-3' => '8', 'XI-4' => '3', 'XI-5' => '16', 'XII-1' => '30P', 'XII-2' => '2', 'XII-3' => '24', 'XII-4' => '27', 'XII-5' => '31']],
                    9 => ['time' => '13:30 - 14:15', 'slots' => ['X-1' => '36', 'X-2' => '15E', 'X-3' => '6', 'X-4' => '7', 'X-5' => '4', 'XI-1' => '12', 'XI-2' => '21', 'XI-3' => '19', 'XI-4' => '3', 'XI-5' => '16', 'XII-1' => '13', 'XII-2' => '2', 'XII-3' => '8', 'XII-4' => '27', 'XII-5' => '22']],
                    10 => ['time' => '14:15 - 15:00', 'slots' => ['X-1' => '36', 'X-2' => '29', 'X-3' => '6', 'X-4' => '7', 'X-5' => '4', 'XI-1' => '12', 'XI-2' => '21', 'XI-3' => '19', 'XI-4' => '3', 'XI-5' => '16', 'XII-1' => '13', 'XII-2' => '2', 'XII-3' => '8', 'XII-4' => '22', 'XII-5' => '22']],
                    'salat_ashar' => ['time' => '15.00 - 15.30', 'label' => 'SALAT ASHAR BERJAMAAH'],
                ],
            ],
            'Selasa' => [
                'piket' => $pikets['Selasa'],
                'periods' => [
                    1 => ['time' => '06:30 - 07:15', 'slots' => ['X-1' => '30P', 'X-2' => '14', 'X-3' => '31', 'X-4' => '35', 'X-5' => '17', 'XI-1' => '21', 'XI-2' => '34', 'XI-3' => '10', 'XI-4' => '33S', 'XI-5' => '18Q', 'XII-1' => '12', 'XII-2' => '7', 'XII-3' => '28', 'XII-4' => '9', 'XII-5' => '13']],
                    2 => ['time' => '07:15 - 08:00', 'slots' => ['X-1' => '30P', 'X-2' => '14', 'X-3' => '31', 'X-4' => '35', 'X-5' => '17', 'XI-1' => '21', 'XI-2' => '34', 'XI-3' => '10', 'XI-4' => '33S', 'XI-5' => '18Q', 'XII-1' => '12', 'XII-2' => '7', 'XII-3' => '28', 'XII-4' => '9', 'XII-5' => '13']],
                    3 => ['time' => '08:00 - 08:45', 'slots' => ['X-1' => '13', 'X-2' => '35', 'X-3' => '14', 'X-4' => '21', 'X-5' => '15E', 'XI-1' => '8', 'XI-2' => '31', 'XI-3' => '10', 'XI-4' => '18Q', 'XI-5' => '33S', 'XII-1' => '9', 'XII-2' => '7', 'XII-3' => '36', 'XII-4' => '25', 'XII-5' => '12']],
                    4 => ['time' => '08:45 - 09:30', 'slots' => ['X-1' => '13', 'X-2' => '35', 'X-3' => '14', 'X-4' => '21', 'X-5' => '15E', 'XI-1' => '8', 'XI-2' => '31', 'XI-3' => '17', 'XI-4' => '18Q', 'XI-5' => '33S', 'XII-1' => '9', 'XII-2' => '24', 'XII-3' => '36', 'XII-4' => '25', 'XII-5' => '12']],
                    'break_1' => ['time' => '09:30 - 09:50', 'label' => 'ISTIRAHAT / SALAT DUHA'],
                    5 => ['time' => '09:50 - 10:35', 'slots' => ['X-1' => '6', 'X-2' => '21', 'X-3' => '4', 'X-4' => '22', 'X-5' => '34', 'XI-1' => '7', 'XI-2' => '10', 'XI-3' => '13', 'XI-4' => '30G', 'XI-5' => '3', 'XII-1' => '23', 'XII-2' => '28', 'XII-3' => '35', 'XII-4' => '16', 'XII-5' => '25']],
                    6 => ['time' => '10:35 - 11:20', 'slots' => ['X-1' => '6', 'X-2' => '21', 'X-3' => '4', 'X-4' => '22', 'X-5' => '34', 'XI-1' => '7', 'XI-2' => '10', 'XI-3' => '13', 'XI-4' => '30G', 'XI-5' => '3', 'XII-1' => '23', 'XII-2' => '28', 'XII-3' => '20', 'XII-4' => '16', 'XII-5' => '25']],
                    7 => ['time' => '11:20 - 12:05', 'slots' => ['X-1' => '6', 'X-2' => '9', 'X-3' => '4', 'X-4' => '29', 'X-5' => '34', 'XI-1' => '7', 'XI-2' => '10', 'XI-3' => '12', 'XI-4' => '30G', 'XI-5' => '3', 'XII-1' => '23', 'XII-2' => '35', 'XII-3' => '20', 'XII-4' => '16', 'XII-5' => '25']],
                    'break_2' => ['time' => '12:05 - 12:45', 'label' => 'ISTIRAHAT DAN SALAT ZUHUR BERJAMAAH'],
                    8 => ['time' => '12:45 - 13:30', 'slots' => ['X-1' => '4', 'X-2' => '9', 'X-3' => '7', 'X-4' => '30P', 'X-5' => '11', 'XI-1' => '27', 'XI-2' => '14', 'XI-3' => '12', 'XI-4' => '16', 'XI-5' => '5', 'XII-1' => '36', 'XII-2' => '23', 'XII-3' => '26', 'XII-4' => '22', 'XII-5' => '31']],
                    9 => ['time' => '13:30 - 14:15', 'slots' => ['X-1' => '4', 'X-2' => '11', 'X-3' => '7', 'X-4' => '15P', 'X-5' => '25', 'XI-1' => '35', 'XI-2' => '14', 'XI-3' => '20', 'XI-4' => '3', 'XI-5' => '8', 'XII-1' => '36', 'XII-2' => '23', 'XII-3' => '26', 'XII-4' => '22', 'XII-5' => '21']],
                    10 => ['time' => '14:15 - 15:00', 'slots' => ['X-1' => '4', 'X-2' => '11', 'X-3' => '35', 'X-4' => '15P', 'X-5' => '25', 'XI-1' => '9', 'XI-2' => '14', 'XI-3' => '20', 'XI-4' => '3', 'XI-5' => '8', 'XII-1' => '36', 'XII-2' => '23', 'XII-3' => '26', 'XII-4' => '30P', 'XII-5' => '21']],
                    'salat_ashar' => ['time' => '15.00 - 15.30', 'label' => 'SALAT ASHAR BERJAMAAH'],
                ],
            ],
            'Rabu' => [
                'piket' => $pikets['Rabu'],
                'periods' => [
                    1 => [
                        'time' => '06:30 - 07:15',
                        'special_merge_x' => 'PRAMUKA',
                        'slots' => ['X-1' => 'PRAMUKA', 'X-2' => 'PRAMUKA', 'X-3' => 'PRAMUKA', 'X-4' => 'PRAMUKA', 'X-5' => 'PRAMUKA', 'XI-1' => '27', 'XI-2' => '32', 'XI-3' => '18A', 'XI-4' => '19', 'XI-5' => '16', 'XII-1' => '7', 'XII-2' => '6', 'XII-3' => '13', 'XII-4' => '21', 'XII-5' => '35']
                    ],
                    2 => [
                        'time' => '07:15 - 08:00',
                        'special_merge_x' => 'PRAMUKA',
                        'slots' => ['X-1' => 'PRAMUKA', 'X-2' => 'PRAMUKA', 'X-3' => 'PRAMUKA', 'X-4' => 'PRAMUKA', 'X-5' => 'PRAMUKA', 'XI-1' => '27', 'XI-2' => '32', 'XI-3' => '18A', 'XI-4' => '19', 'XI-5' => '16', 'XII-1' => '7', 'XII-2' => '6', 'XII-3' => '13', 'XII-4' => '21', 'XII-5' => '20']
                    ],
                    3 => ['time' => '08:00 - 08:45', 'slots' => ['X-1' => '32', 'X-2' => '1', 'X-3' => '3', 'X-4' => '17', 'X-5' => '35', 'XI-1' => '33P', 'XI-2' => '10', 'XI-3' => '34', 'XI-4' => '16', 'XI-5' => '31', 'XII-1' => '36', 'XII-2' => '30P', 'XII-3' => '21', 'XII-4' => '8', 'XII-5' => '20']],
                    4 => ['time' => '08:45 - 09:30', 'slots' => ['X-1' => '32', 'X-2' => '1', 'X-3' => '3', 'X-4' => '17', 'X-5' => '35', 'XI-1' => '33P', 'XI-2' => '10', 'XI-3' => '34', 'XI-4' => '16', 'XI-5' => '31', 'XII-1' => '36', 'XII-2' => '30P', 'XII-3' => '21', 'XII-4' => '8', 'XII-5' => '20']],
                    'break_1' => ['time' => '09:30 - 09:50', 'label' => 'ISTIRAHAT / SALAT DUHA'],
                    5 => ['time' => '09:50 - 10:35', 'slots' => ['X-1' => '15E', 'X-2' => '25', 'X-3' => '36', 'X-4' => '6', 'X-5' => '17', 'XI-1' => '32', 'XI-2' => '7', 'XI-3' => '27', 'XI-4' => '18A', 'XI-5' => '2', 'XII-1' => '28', 'XII-2' => '33S', 'XII-3' => '4', 'XII-4' => '35', 'XII-5' => '3']],
                    6 => ['time' => '10:35 - 11:20', 'slots' => ['X-1' => '15E', 'X-2' => '25', 'X-3' => '36', 'X-4' => '6', 'X-5' => '17', 'XI-1' => '32', 'XI-2' => '7', 'XI-3' => '27', 'XI-4' => '18A', 'XI-5' => '2', 'XII-1' => '28', 'XII-2' => '33S', 'XII-3' => '4', 'XII-4' => '31', 'XII-5' => '3']],
                    7 => ['time' => '11:20 - 12:05', 'slots' => ['X-1' => '9', 'X-2' => '17', 'X-3' => '11', 'X-4' => '6', 'X-5' => '21', 'XI-1' => '32', 'XI-2' => '7', 'XI-3' => '27', 'XI-4' => '35', 'XI-5' => '2', 'XII-1' => '24', 'XII-2' => '22', 'XII-3' => '4', 'XII-4' => '31', 'XII-5' => '3']],
                    'break_2' => ['time' => '12:05 - 12:45', 'label' => 'ISTIRAHAT DAN SALAT ZUHUR BERJAMAAH'],
                    8 => ['time' => '12:45 - 13:30', 'slots' => ['X-1' => '9', 'X-2' => '4', 'X-3' => '11', 'X-4' => '36', 'X-5' => '21', 'XI-1' => '10', 'XI-2' => '8', 'XI-3' => '5', 'XI-4' => '27', 'XI-5' => '35', 'XII-1' => '26', 'XII-2' => '22', 'XII-3' => '20', 'XII-4' => '30P', 'XII-5' => '24']],
                    9 => ['time' => '13:30 - 14:15', 'slots' => ['X-1' => '7', 'X-2' => '4', 'X-3' => '32', 'X-4' => '11', 'X-5' => '15P', 'XI-1' => '10', 'XI-2' => '8', 'XI-3' => '31', 'XI-4' => '27', 'XI-5' => '22', 'XII-1' => '26', 'XII-2' => '19', 'XII-3' => '20', 'XII-4' => '33S', 'XII-5' => '30P']],
                    10 => ['time' => '14:15 - 15:00', 'slots' => ['X-1' => '7', 'X-2' => '4', 'X-3' => '32', 'X-4' => '11', 'X-5' => '15P', 'XI-1' => '10', 'XI-2' => '35', 'XI-3' => '31', 'XI-4' => '27', 'XI-5' => '22', 'XII-1' => '26', 'XII-2' => '19', 'XII-3' => '20', 'XII-4' => '33S', 'XII-5' => '30P']],
                    'salat_ashar' => ['time' => '15.00 - 15.30', 'label' => 'SALAT ASHAR BERJAMAAH'],
                ],
            ],
            'Kamis' => [
                'piket' => $pikets['Kamis'],
                'periods' => [
                    1 => ['time' => '06:30 - 07:15', 'slots' => ['X-1' => '22', 'X-2' => '32', 'X-3' => '30P', 'X-4' => '14', 'X-5' => '13', 'XI-1' => '19', 'XI-2' => '7', 'XI-3' => '18Q', 'XI-4' => '12', 'XI-5' => '25', 'XII-1' => '8', 'XII-2' => '2', 'XII-3' => '4', 'XII-4' => '28', 'XII-5' => '20']],
                    2 => ['time' => '07:15 - 08:00', 'slots' => ['X-1' => '22', 'X-2' => '32', 'X-3' => '30P', 'X-4' => '14', 'X-5' => '13', 'XI-1' => '19', 'XI-2' => '7', 'XI-3' => '18Q', 'XI-4' => '12', 'XI-5' => '25', 'XII-1' => '8', 'XII-2' => '2', 'XII-3' => '4', 'XII-4' => '28', 'XII-5' => '20']],
                    3 => ['time' => '08:00 - 08:45', 'slots' => ['X-1' => '1', 'X-2' => '36', 'X-3' => '34', 'X-4' => '3', 'X-5' => '14', 'XI-1' => '22', 'XI-2' => '12', 'XI-3' => '27', 'XI-4' => '13', 'XI-5' => '18A', 'XII-1' => '19', 'XII-2' => '8', 'XII-3' => '30P', 'XII-4' => '11', 'XII-5' => '25']],
                    4 => ['time' => '08:45 - 09:30', 'slots' => ['X-1' => '1', 'X-2' => '36', 'X-3' => '34', 'X-4' => '3', 'X-5' => '14', 'XI-1' => '22', 'XI-2' => '12', 'XI-3' => '27', 'XI-4' => '13', 'XI-5' => '18A', 'XII-1' => '19', 'XII-2' => '8', 'XII-3' => '30P', 'XII-4' => '11', 'XII-5' => '25']],
                    'break_1' => ['time' => '09:30 - 09:50', 'label' => 'ISTIRAHAT / SALAT DUHA'],
                    5 => ['time' => '09:50 - 10:35', 'slots' => ['X-1' => '17', 'X-2' => '22', 'X-3' => '34', 'X-4' => '25', 'X-5' => '31', 'XI-1' => '10', 'XI-2' => '32', 'XI-3' => '20', 'XI-4' => '33P', 'XI-5' => '3', 'XII-1' => '14', 'XII-2' => '6', 'XII-3' => '8', 'XII-4' => '19', 'XII-5' => '28']],
                    6 => ['time' => '10:35 - 11:20', 'slots' => ['X-1' => '17', 'X-2' => '22', 'X-3' => '15P', 'X-4' => '25', 'X-5' => '31', 'XI-1' => '10', 'XI-2' => '32', 'XI-3' => '20', 'XI-4' => '33P', 'XI-5' => '3', 'XII-1' => '14', 'XII-2' => '6', 'XII-3' => '8', 'XII-4' => '19', 'XII-5' => '28']],
                    7 => ['time' => '11:20 - 12:05', 'slots' => ['X-1' => '25', 'X-2' => '31', 'X-3' => '15P', 'X-4' => '30P', 'X-5' => '11', 'XI-1' => '10', 'XI-2' => '32', 'XI-3' => '20', 'XI-4' => '5', 'XI-5' => '19', 'XII-1' => '14', 'XII-2' => '6', 'XII-3' => '8', 'XII-4' => '13', 'XII-5' => '3']],
                    'break_2' => ['time' => '12:05 - 12:45', 'label' => 'ISTIRAHAT DAN SALAT ZUHUR BERJAMAAH'],
                    8 => ['time' => '12:45 - 13:30', 'slots' => ['X-1' => '25', 'X-2' => '31', 'X-3' => '17', 'X-4' => '4', 'X-5' => '6', 'XI-1' => '20', 'XI-2' => '5', 'XI-3' => '35', 'XI-4' => '10', 'XI-5' => '19', 'XII-1' => '30P', 'XII-2' => '26', 'XII-3' => '23', 'XII-4' => '13', 'XII-5' => '3']],
                    9 => ['time' => '13:30 - 14:15', 'slots' => ['X-1' => '11', 'X-2' => '15P', 'X-3' => '17', 'X-4' => '4', 'X-5' => '6', 'XI-1' => '20', 'XI-2' => '27', 'XI-3' => '33P', 'XI-4' => '10', 'XI-5' => '13', 'XII-1' => '31', 'XII-2' => '26', 'XII-3' => '23', 'XII-4' => '3', 'XII-5' => '9']],
                    10 => ['time' => '14:15 - 15:00', 'slots' => ['X-1' => '11', 'X-2' => '15P', 'X-3' => '29', 'X-4' => '4', 'X-5' => '6', 'XI-1' => '20', 'XI-2' => '27', 'XI-3' => '33P', 'XI-4' => '10', 'XI-5' => '13', 'XII-1' => '31', 'XII-2' => '26', 'XII-3' => '23', 'XII-4' => '3', 'XII-5' => '9']],
                    'salat_ashar' => ['time' => '15.00 - 15.30', 'label' => 'SALAT ASHAR BERJAMAAH'],
                ],
            ],
            'Jumat' => [
                'piket' => $pikets['Jumat'],
                'periods' => [
                    1 => ['time' => '06:30 - 07:15', 'slots' => ['X-1' => '3', 'X-2' => '7', 'X-3' => '22', 'X-4' => '34', 'X-5' => '36', 'XI-1' => '32', 'XI-2' => '10', 'XI-3' => '17', 'XI-4' => '31', 'XI-5' => '2', 'XII-1' => '14', 'XII-2' => '13', 'XII-3' => '19', 'XII-4' => '25', 'XII-5' => '8']],
                    2 => ['time' => '07:15 - 08:00', 'slots' => ['X-1' => '3', 'X-2' => '7', 'X-3' => '22', 'X-4' => '34', 'X-5' => '36', 'XI-1' => '32', 'XI-2' => '10', 'XI-3' => '17', 'XI-4' => '31', 'XI-5' => '2', 'XII-1' => '14', 'XII-2' => '13', 'XII-3' => '19', 'XII-4' => '25', 'XII-5' => '8']],
                    3 => ['time' => '08:00 - 08:45', 'slots' => ['X-1' => '15P', 'X-2' => '17', 'X-3' => '12', 'X-4' => '34', 'X-5' => '7', 'XI-1' => '13', 'XI-2' => '10', 'XI-3' => '22', 'XI-4' => '16', 'XI-5' => '33P', 'XII-1' => '6', 'XII-2' => '14', 'XII-3' => '9', 'XII-4' => '25', 'XII-5' => '23']],
                    4 => ['time' => '08:45 - 09:30', 'slots' => ['X-1' => '15P', 'X-2' => '14', 'X-3' => '12', 'X-4' => '36', 'X-5' => '7', 'XI-1' => '13', 'XI-2' => '27', 'XI-3' => '22', 'XI-4' => '16', 'XI-5' => '33P', 'XII-1' => '6', 'XII-2' => '14', 'XII-3' => '9', 'XII-4' => '25', 'XII-5' => '23']],
                    'break_1' => ['time' => '09:30 - 09:50', 'label' => 'ISTIRAHAT / SALAT DUHA'],
                    5 => ['time' => '09:50 - 10:35', 'slots' => ['X-1' => '21', 'X-2' => '34', 'X-3' => '15P', 'X-4' => '17', 'X-5' => '32', 'XI-1' => '10', 'XI-2' => '9', 'XI-3' => '2', 'XI-4' => '30G', 'XI-5' => '27', 'XII-1' => '33S', 'XII-2' => '12', 'XII-3' => '8', 'XII-4' => '3', 'XII-5' => '23']],
                    6 => ['time' => '10:35 - 11:20', 'slots' => ['X-1' => '21', 'X-2' => '34', 'X-3' => '15P', 'X-4' => '17', 'X-5' => '32', 'XI-1' => '10', 'XI-2' => '9', 'XI-3' => '2', 'XI-4' => '30G', 'XI-5' => '27', 'XII-1' => '33S', 'XII-2' => '12', 'XII-3' => '8', 'XII-4' => '3', 'XII-5' => '16']],
                    'break_2' => ['time' => '11:20 - 12:30', 'label' => 'ISTIRAHAT DAN SALAT JUMAT/ZUHUR BERJAMAAH'],
                    7 => ['time' => '12:30 - 13:15', 'slots' => ['X-1' => '14', 'X-2' => '30P', 'X-3' => '13', 'X-4' => '12', 'X-5' => '9', 'XI-1' => '20', 'XI-2' => '22', 'XI-3' => '2', 'XI-4' => '25', 'XI-5' => '27', 'XII-1' => '21', 'XII-2' => '7', 'XII-3' => '33S', 'XII-4' => '11', 'XII-5' => '16']],
                    8 => ['time' => '13:15 - 14:00', 'slots' => ['X-1' => '14', 'X-2' => '30P', 'X-3' => '13', 'X-4' => '12', 'X-5' => '9', 'XI-1' => '20', 'XI-2' => '22', 'XI-3' => '26', 'XI-4' => '25', 'XI-5' => '10', 'XII-1' => '21', 'XII-2' => '7', 'XII-3' => '33S', 'XII-4' => '3', 'XII-5' => '16']],
                    9 => ['time' => '14:00 - 14:45', 'slots' => ['X-1' => '17', 'X-2' => '3', 'X-3' => '25', 'X-4' => '32', 'X-5' => '30P', 'XI-1' => '31', 'XI-2' => '33P', 'XI-3' => '26', 'XI-4' => '8', 'XI-5' => '10', 'XII-1' => '22', 'XII-2' => '21', 'XII-3' => '12', 'XII-4' => '11', 'XII-5' => '19']],
                    10 => ['time' => '14:45 - 15:30', 'slots' => ['X-1' => '17', 'X-2' => '3', 'X-3' => '25', 'X-4' => '32', 'X-5' => '30P', 'XI-1' => '31', 'XI-2' => '33P', 'XI-3' => '26', 'XI-4' => '8', 'XI-5' => '10', 'XII-1' => '22', 'XII-2' => '21', 'XII-3' => '12', 'XII-4' => '11', 'XII-5' => '19']],
                    'salat_ashar' => ['time' => '15.30 - 16.00', 'label' => 'SALAT ASHAR BERJAMAAH'],
                ],
            ],
        ];
    }

    public static function getTeachersList(): array
    {
        return [
            1 => ['name' => 'Drs. SODIKIN M.Si', 'subject' => 'SKI', 'days' => [0, 0, 2, 2, 0], 'total' => 4],
            2 => ['name' => 'EFFIYATI PRIHATINI S.Pd, M.Pd', 'subject' => 'Biologi TL', 'days' => [5, 0, 3, 2, 5], 'total' => 15],
            3 => ['name' => 'DENI SUTEDI, MM.Pd', 'subject' => 'Sosiologi ( Wajib dan TL )', 'days' => [5, 5, 5, 8, 7], 'total' => 30],
            4 => ['name' => 'SITI RUWAIDAH S.Pd', 'subject' => 'Bahasa Indonesia (Wajib dan TL)', 'days' => [3, 6, 6, 5, 0], 'total' => 20],
            5 => ['name' => 'NANANG KOSIM S.Pd', 'subject' => 'BK', 'days' => [1, 1, 1, 2, 0], 'total' => 25],
            6 => ['name' => 'ETI RUSMIATI, S.Pd.,M.Pd', 'subject' => 'Matematika ( Wajib dan TL )', 'days' => [9, 3, 5, 6, 2], 'total' => 25],
            7 => ['name' => 'ENDAH ISMIYATI, S.Pd', 'subject' => 'Kimia ( Wajib dan TL )', 'days' => [8, 7, 8, 2, 6], 'total' => 30],
            8 => ['name' => 'FIRMANSYAH, S.Ag.,M.Hum', 'subject' => 'Bahasa Arab ( Wajib dan TL )', 'days' => [4, 4, 4, 7, 6], 'total' => 25],
            9 => ['name' => 'SITI HASANAH, S.Ag', 'subject' => 'Akidah Akhlak', 'days' => [7, 7, 2, 2, 6], 'total' => 24],
            10 => ['name' => 'MIFTAHUL JANNAH, S.Pd.,M.Si', 'subject' => 'Matematika ( Wajib dan TL )', 'days' => [0, 6, 5, 6, 8], 'total' => 25],
            11 => ['name' => 'TEMY YULIANTI, S.Pd.,M.Pd', 'subject' => 'Geografi ( Wajib dan TL )', 'days' => [0, 3, 4, 5, 3], 'total' => 15],
            12 => ['name' => 'IIS SURYANI, S.Pd.I', 'subject' => 'SKI', 'days' => [8, 6, 0, 4, 8], 'total' => 26],
            13 => ['name' => 'IPONG ROHIMAH, S.Ag', 'subject' => 'Fikih', 'days' => [8, 6, 2, 8, 6], 'total' => 30],
            14 => ['name' => 'LINDA AGUNG, S.TP', 'subject' => 'Fisika ( Wajib dan TL )', 'days' => [5, 7, 0, 7, 6], 'total' => 25],
            15 => ['name' => 'HASAN BASRI, S.Pd., M.Si', 'subject' => 'Ekonomi Dan Keterampilan', 'days' => [4, 4, 4, 4, 4], 'total' => 20],
            16 => ['name' => 'SITI BAROROH, S.Pd', 'subject' => 'Ekonomi TL', 'days' => [7, 4, 4, 0, 5], 'total' => 20],
            17 => ['name' => 'OMID SYAMSUDIN, S.Ag', 'subject' => 'Bahasa Arab ( Wajib dan TL )', 'days' => [6, 3, 5, 4, 7], 'total' => 25],
            18 => ['name' => 'HIDAYAT MUSTOFA, S.Pd.I', 'subject' => 'Quran Hadist Dan AA', 'days' => [0, 4, 4, 4, 0], 'total' => 12],
            19 => ['name' => 'MIFTAH EL FALAH, SE', 'subject' => 'Keterampilan', 'days' => [4, 0, 4, 8, 4], 'total' => 20],
            20 => ['name' => 'WAHYU UJIANTO, S.S', 'subject' => 'Bahasa Inggris TL', 'days' => [0, 4, 6, 8, 2], 'total' => 20],
            21 => ['name' => 'ALI SUBHAN, S.Ag', 'subject' => 'Quran Hadist', 'days' => [4, 8, 6, 0, 6], 'total' => 24],
            22 => ['name' => 'FERIYA HASTUTI, S.S', 'subject' => 'Bahasa Inggris', 'days' => [8, 4, 4, 6, 8], 'total' => 30],
            23 => ['name' => 'RIZAL, S.Pd', 'subject' => 'Matematika ( Wajib )', 'days' => [3, 6, 0, 3, 3], 'total' => 15],
            24 => ['name' => 'NURHASANAH, S.Pd', 'subject' => 'BK', 'days' => [1, 1, 2, 0, 1], 'total' => 25],
            25 => ['name' => 'PUTRI DWI LESTARI, S.Pd', 'subject' => 'Sejarah ( Wajib dan TL )', 'days' => [6, 7, 2, 8, 7], 'total' => 30],
            26 => ['name' => 'MUHAMMAD SUWANDI, S.Pd', 'subject' => 'Bahasa Indonesia (W dan TL )', 'days' => [0, 3, 3, 3, 3], 'total' => 12],
            27 => ['name' => 'LEYLIA ARUM AMALUN NISA, S.Pd', 'subject' => 'Bahasa Indonesia (w dan TL)', 'days' => [6, 1, 8, 4, 4], 'total' => 23],
            28 => ['name' => 'ARIEL BONARDO H. LIMBONG, S.Pd', 'subject' => 'PJOK', 'days' => [4, 4, 2, 4, 0], 'total' => 14],
            29 => ['name' => 'CITRA ARIYANTI, S.Sos', 'subject' => 'Sosiologi', 'days' => [3, 1, 0, 1, 0], 'total' => 25],
            30 => ['name' => 'HANNY MILLENIA, S.Pd', 'subject' => 'Pend. Pancasila Dan Geo TL', 'days' => [1, 7, 5, 6, 6], 'total' => 25],
            31 => ['name' => 'LINTANG ADI RAMADHAN, S.Pd', 'subject' => 'Bahasa Inggris', 'days' => [9, 5, 6, 6, 4], 'total' => 30],
            32 => ['name' => 'MOHAMAD IKHLAS, S.Pd', 'subject' => 'Biologi ( Wajib dan TL )', 'days' => [0, 0, 9, 5, 6], 'total' => 20],
            33 => ['name' => 'DIKA SAVITRI, S.Pd', 'subject' => 'Pend. Pancasila Dan SI', 'days' => [8, 4, 6, 4, 8], 'total' => 30],
            34 => ['name' => 'DANDY ARIF RINALDI, S.Pd', 'subject' => 'PJOK', 'days' => [5, 5, 2, 3, 6], 'total' => 21],
            35 => ['name' => 'MUHAMAD RICO SAPUTRA, S.Pd', 'subject' => 'Hafalan Quran', 'days' => [4, 8, 7, 1, 0], 'total' => 20],
            36 => ['name' => 'DIEGO WAHYU RAMADHAN, S.Kom', 'subject' => 'Informatika', 'days' => [5, 5, 5, 2, 3], 'total' => 20],
        ];
    }

    public static function getHomeroomTeachers(): array
    {
        return [
            1 => ['name' => 'LINDA AGUNG, S.TP', 'class' => 'X-1'],
            2 => ['name' => 'SITI RUWAIDAH, S.Pd.', 'class' => 'X-2'],
            3 => ['name' => 'M. IKHLAS, S.Pd.', 'class' => 'X-3'],
            4 => ['name' => 'PUTRI DWI LESTARI S.Pd', 'class' => 'X-4'],
            5 => ['name' => 'IPONG ROHIMAH, S.Ag', 'class' => 'X-5'],
            6 => ['name' => 'MIFTAH EL FALAH, S.Pd', 'class' => 'XI-1'],
            7 => ['name' => 'MIFTAHUL JANNAH, M.Si.', 'class' => 'XI-2'],
            8 => ['name' => 'LEYLIA ARUM AMALUN NISA S.Pd', 'class' => 'XI-3'],
            9 => ['name' => 'HANNY MILLENIA S.Pd', 'class' => 'XI-4'],
            10 => ['name' => 'FERIYA HASTUTI, S.S.', 'class' => 'XI-5'],
            11 => ['name' => 'ALI SUBHAN, S.Ag.', 'class' => 'XII-1'],
            12 => ['name' => 'SITI HASANAH, S.Ag.', 'class' => 'XII-2'],
            13 => ['name' => 'FIRMANSYAH, S.Ag., M.Hum.', 'class' => 'XII-3'],
            14 => ['name' => 'DENI SUTEDI, MM.Pd.', 'class' => 'XII-4'],
            15 => ['name' => 'SITI BAROROH S.Pd', 'class' => 'XII-5'],
        ];
    }
}
