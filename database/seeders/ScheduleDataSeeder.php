<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Classroom;
use App\Models\Subject;
use App\Models\TeacherAssignment;
use App\Models\TimeSlot;
use App\Models\SchoolSetting;
use App\Models\Schedule;
use App\Services\ScheduleGeneratorService;
use App\Services\OfficialScheduleData;
use Illuminate\Support\Facades\Hash;

class ScheduleDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Login Admin
        User::updateOrCreate(
            ['email' => 'admin@man20.sch.id'],
            [
                'name' => 'Administrator MAN 20',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'ilhamgans@gmail.com'],
            [
                'name' => 'Ilham Ramadhan (Admin)',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
            ]
        );

        // 2. Pengaturan Identitas Madrasah
        $settings = [
            'school_name' => 'MADRASAH ALIYAH NEGERI 20 JAKARTA TIMUR',
            'school_address' => 'Jl. Swadaya No. 20, Duren Sawit, Jakarta Timur',
            'academic_year' => '2026/2027',
            'semester' => 'Ganjil',
            'principal_name' => 'Drs. SODIKIN, M.Si.',
            'principal_nip' => '196805101994031002',
            'waka_kurikulum' => 'TEMY YULIANTI, S.Pd., M.Pd.',
        ];
        foreach ($settings as $key => $val) {
            SchoolSetting::updateOrCreate(['key' => $key], ['value' => $val]);
        }

        // 3. 15 Rombongan Belajar (Kelas X, XI, XII)
        $classes = [
            'X.1' => 10, 'X.2' => 10, 'X.3' => 10, 'X.4' => 10, 'X.5' => 10,
            'XI.1' => 11, 'XI.2' => 11, 'XI.3' => 11, 'XI.4' => 11, 'XI.5' => 11,
            'XII.1' => 12, 'XII.2' => 12, 'XII.3' => 12, 'XII.4' => 12, 'XII.5' => 12,
        ];
        $classroomMap = [];
        foreach ($classes as $name => $grade) {
            $c = Classroom::updateOrCreate(['name' => $name], ['grade' => $grade]);
            $classroomMap[$name] = $c->id;
        }

        // 4. Slot Waktu Jam Pelajaran (Senin - Jumat)
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        $periods = [
            ['period' => 1, 'start' => '06:45', 'end' => '07:30'],
            ['period' => 2, 'start' => '07:30', 'end' => '08:15'],
            ['period' => 3, 'start' => '08:15', 'end' => '09:00'],
            ['period' => 4, 'start' => '09:00', 'end' => '09:45'],
            ['period' => 5, 'start' => '10:15', 'end' => '11:00'],
            ['period' => 6, 'start' => '11:00', 'end' => '11:45'],
            ['period' => 7, 'start' => '11:45', 'end' => '12:30'],
            ['period' => 8, 'start' => '13:00', 'end' => '13:45'],
            ['period' => 9, 'start' => '13:45', 'end' => '14:30'],
            ['period' => 10, 'start' => '14:30', 'end' => '15:15'],
        ];
        foreach ($days as $day) {
            foreach ($periods as $p) {
                TimeSlot::updateOrCreate(
                    ['day' => $day, 'period' => $p['period']],
                    ['start_time' => $p['start'], 'end_time' => $p['end'], 'is_break' => false]
                );
            }
        }

        // 5. Data 37 Mata Pelajaran (Kurikulum Merdeka MAN 20 Jakarta)
        $subjectsData = [
            'SKI' => ['code' => 'SKI', 'category' => 'Agama', 'color' => '#0284C7'],
            'BIOLOGI TINGKAT LANJUT' => ['code' => 'BIOTL', 'category' => 'Tingkat Lanjut', 'color' => '#059669'],
            'SOSIOLOGI' => ['code' => 'SOS', 'category' => 'Wajib', 'color' => '#D97706'],
            'SOSIOLOGI TINGKAT LANJUT' => ['code' => 'SOSTL', 'category' => 'Tingkat Lanjut', 'color' => '#B45309'],
            'BAHASA INDONESIA WAJIB' => ['code' => 'BAHINDWA', 'category' => 'Wajib', 'color' => '#DC2626'],
            'BAHASA INDONESIA TINGKAT LANJUT' => ['code' => 'BAHINDTL', 'category' => 'Tingkat Lanjut', 'color' => '#991B1B'],
            'BK' => ['code' => 'BK', 'category' => 'Wajib', 'color' => '#6B7280'],
            'MATEMATIKA WAJIB' => ['code' => 'MATWAJ', 'category' => 'Wajib', 'color' => '#2563EB'],
            'MATEMATIKA TINGKAT LANJUT' => ['code' => 'MATTL', 'category' => 'Tingkat Lanjut', 'color' => '#1D4ED8'],
            'KIMIA' => ['code' => 'KIM', 'category' => 'Wajib', 'color' => '#7C3AED'],
            'KIMIA TINGKAT LANJUT' => ['code' => 'KIMTL', 'category' => 'Tingkat Lanjut', 'color' => '#6D28D9'],
            'BAHASA ARAB WAJIB' => ['code' => 'BAHARAWA', 'category' => 'Agama', 'color' => '#0D9488'],
            'BAHASA ARAB TINGKAT LANJUT' => ['code' => 'BAHARATL', 'category' => 'Tingkat Lanjut', 'color' => '#0F766E'],
            'AKIDAH AKHLAK' => ['code' => 'AKIAKH', 'category' => 'Agama', 'color' => '#4F46E5'],
            'GEOGRAFI' => ['code' => 'GEO', 'category' => 'Wajib', 'color' => '#CA8A04'],
            'GEOGRAFI TINGKAT LANJUT' => ['code' => 'GEOTL', 'category' => 'Tingkat Lanjut', 'color' => '#A16207'],
            'FIQIH' => ['code' => 'FIQ', 'category' => 'Agama', 'color' => '#047857'],
            'FISIKA' => ['code' => 'FIS', 'category' => 'Wajib', 'color' => '#E11D48'],
            'FISIKA TINGKAT LANJUT' => ['code' => 'FISTL', 'category' => 'Tingkat Lanjut', 'color' => '#BE123C'],
            'EKONOMI' => ['code' => 'EKO', 'category' => 'Wajib', 'color' => '#EA580C'],
            'KETERAMPILAN' => ['code' => 'KET', 'category' => 'Wajib', 'color' => '#84CC16'],
            'EKONOMI TINGKAT LANJUT' => ['code' => 'EKOTL', 'category' => 'Tingkat Lanjut', 'color' => '#C2410C'],
            'QURDIS' => ['code' => 'QUR', 'category' => 'Wajib', 'color' => '#065F46'],
            'BAHASA INGGRIS TINGKAT LANJUT' => ['code' => 'BAHINGTL', 'category' => 'Tingkat Lanjut', 'color' => '#1E40AF'],
            'ALQURAN HADIST' => ['code' => 'ALQHAD', 'category' => 'Agama', 'color' => '#047857'],
            'BAHASA INGGRIS WAJIB' => ['code' => 'BAHINGWA', 'category' => 'Wajib', 'color' => '#3B82F6'],
            'SEJARAH WAJIB' => ['code' => 'SEJWAJ', 'category' => 'Wajib', 'color' => '#9333EA'],
            'SEJARAH TINGKAT LANJUT' => ['code' => 'SEJTL', 'category' => 'Tingkat Lanjut', 'color' => '#6B21A8'],
            'PJOK' => ['code' => 'PJO', 'category' => 'Wajib', 'color' => '#14B8A6'],
            'PENDIDIKAN PANCASILA' => ['code' => 'PENPAN', 'category' => 'Wajib', 'color' => '#EF4444'],
            'SBK' => ['code' => 'SBK', 'category' => 'Wajib', 'color' => '#F59E0B'],
            'BIOLOGI' => ['code' => 'BIO', 'category' => 'Wajib', 'color' => '#10B981'],
            'SEJARAH INDONESIA' => ['code' => 'SEJIND', 'category' => 'Wajib', 'color' => '#7E22CE'],
            'HIFZIL QURAN' => ['code' => 'HIFQUR', 'category' => 'Agama', 'color' => '#15803D'],
            'INFORMATIKA' => ['code' => 'INF', 'category' => 'Wajib', 'color' => '#06B6D4'],
            'INFORMATIKA TINGKAT LANJUT' => ['code' => 'INFTL', 'category' => 'Tingkat Lanjut', 'color' => '#0891B2'],
            'Pramuka Wajib' => ['code' => 'PRAM', 'category' => 'Wajib', 'color' => '#854D0E'],
        ];

        $subjectMap = [];
        foreach ($subjectsData as $sName => $sInfo) {
            $sub = Subject::updateOrCreate(
                ['name' => $sName],
                ['code' => $sInfo['code'], 'category' => $sInfo['category'], 'color' => $sInfo['color']]
            );
            $subjectMap[$sName] = $sub->id;
        }

        // 6. Data Lengkap 36 Guru & Distribusi Pembagian Tugas Mengajar (Berdasarkan Dokumen Excel)
        $teachersData = [
            [
                'order_no' => 1,
                'name' => 'Drs. SODIKIN, M.Si.',
                'target_hours' => 28,
                'additional_role' => 'KEPALA MADRASAH',
                'assignments' => [
                    ['subject' => 'SKI', 'classes' => [
                        ['classroom' => 'X.1', 'hours' => 2],
                        ['classroom' => 'X.2', 'hours' => 2],
                    ]],
                ],
            ],
            [
                'order_no' => 2,
                'name' => 'EFFIYATI PRIHATINI, S.Pd, M.Pd.',
                'target_hours' => 27,
                'additional_role' => 'KEPALA LABORATORIUM',
                'assignments' => [
                    ['subject' => 'BIOLOGI TINGKAT LANJUT', 'classes' => [
                        ['classroom' => 'XI.3', 'hours' => 5],
                        ['classroom' => 'XI.5', 'hours' => 5],
                        ['classroom' => 'XII.2', 'hours' => 5],
                    ]],
                ],
            ],
            [
                'order_no' => 3,
                'name' => 'DENI SUTEDI, MM.Pd.',
                'target_hours' => 36,
                'additional_role' => 'WALI KELAS',
                'assignments' => [
                    ['subject' => 'SOSIOLOGI', 'classes' => [
                        ['classroom' => 'X.1', 'hours' => 2],
                        ['classroom' => 'X.2', 'hours' => 2],
                        ['classroom' => 'X.3', 'hours' => 2],
                        ['classroom' => 'X.4', 'hours' => 2],
                        ['classroom' => 'X.5', 'hours' => 2],
                    ]],
                    ['subject' => 'SOSIOLOGI TINGKAT LANJUT', 'classes' => [
                        ['classroom' => 'XI.4', 'hours' => 5],
                        ['classroom' => 'XI.5', 'hours' => 5],
                        ['classroom' => 'XII.4', 'hours' => 5],
                        ['classroom' => 'XII.5', 'hours' => 5],
                    ]],
                ],
            ],
            [
                'order_no' => 4,
                'name' => 'SITI RUWAIDAH, S.Pd.',
                'target_hours' => 29,
                'additional_role' => 'WALI KELAS, PEMBINA  EKSKUL, PIKET',
                'assignments' => [
                    ['subject' => 'BAHASA INDONESIA WAJIB', 'classes' => [
                        ['classroom' => 'X.1', 'hours' => 3],
                        ['classroom' => 'X.2', 'hours' => 3],
                        ['classroom' => 'X.3', 'hours' => 3],
                        ['classroom' => 'X.4', 'hours' => 3],
                        ['classroom' => 'X.5', 'hours' => 3],
                    ]],
                    ['subject' => 'BAHASA INDONESIA TINGKAT LANJUT', 'classes' => [
                        ['classroom' => 'XII.3', 'hours' => 5],
                    ]],
                ],
            ],
            [
                'order_no' => 5,
                'name' => 'NANANG KOSIM, S.Pd.',
                'target_hours' => 26,
                'additional_role' => 'Piket',
                'assignments' => [
                    ['subject' => 'BK', 'classes' => [
                        ['classroom' => 'XI.1', 'hours' => 1],
                        ['classroom' => 'XI.2', 'hours' => 1],
                        ['classroom' => 'XI.3', 'hours' => 1],
                        ['classroom' => 'XI.4', 'hours' => 1],
                        ['classroom' => 'XI.5', 'hours' => 1],
                    ]],
                ],
            ],
            [
                'order_no' => 6,
                'name' => 'ETI RUSMIATI, M.Pd.',
                'target_hours' => 25,
                'additional_role' => '',
                'assignments' => [
                    ['subject' => 'MATEMATIKA WAJIB', 'classes' => [
                        ['classroom' => 'X.1', 'hours' => 3],
                        ['classroom' => 'X.2', 'hours' => 3],
                        ['classroom' => 'X.3', 'hours' => 3],
                        ['classroom' => 'X.4', 'hours' => 3],
                        ['classroom' => 'X.5', 'hours' => 3],
                    ]],
                    ['subject' => 'MATEMATIKA TINGKAT LANJUT', 'classes' => [
                        ['classroom' => 'XII.1', 'hours' => 5],
                        ['classroom' => 'XII.2', 'hours' => 5],
                    ]],
                ],
            ],
            [
                'order_no' => 7,
                'name' => 'ENDAH ISMIYATI, S.Pd.',
                'target_hours' => 30,
                'additional_role' => '',
                'assignments' => [
                    ['subject' => 'KIMIA', 'classes' => [
                        ['classroom' => 'X.1', 'hours' => 2],
                        ['classroom' => 'X.2', 'hours' => 2],
                        ['classroom' => 'X.3', 'hours' => 2],
                        ['classroom' => 'X.4', 'hours' => 2],
                        ['classroom' => 'X.5', 'hours' => 2],
                    ]],
                    ['subject' => 'KIMIA TINGKAT LANJUT', 'classes' => [
                        ['classroom' => 'XI.1', 'hours' => 5],
                        ['classroom' => 'XI.2', 'hours' => 5],
                        ['classroom' => 'XII.1', 'hours' => 5],
                        ['classroom' => 'XII.2', 'hours' => 5],
                    ]],
                ],
            ],
            [
                'order_no' => 8,
                'name' => 'FIRMANSYAH, S.Ag.,M.Hum.',
                'target_hours' => 34,
                'additional_role' => 'WALI KELAS, PEMBINA  EKSKUL, PIKET',
                'assignments' => [
                    ['subject' => 'BAHASA ARAB WAJIB', 'classes' => [
                        ['classroom' => 'XI.1', 'hours' => 2],
                        ['classroom' => 'XI.2', 'hours' => 2],
                        ['classroom' => 'XI.3', 'hours' => 2],
                        ['classroom' => 'XI.4', 'hours' => 2],
                        ['classroom' => 'XI.5', 'hours' => 2],
                        ['classroom' => 'XII.1', 'hours' => 2],
                        ['classroom' => 'XII.2', 'hours' => 2],
                        ['classroom' => 'XII.3', 'hours' => 2],
                        ['classroom' => 'XII.4', 'hours' => 2],
                        ['classroom' => 'XII.5', 'hours' => 2],
                    ]],
                    ['subject' => 'BAHASA ARAB TINGKAT LANJUT', 'classes' => [
                        ['classroom' => 'XII.3', 'hours' => 5],
                    ]],
                ],
            ],
            [
                'order_no' => 9,
                'name' => 'SITI HASANAH, S.Ag.',
                'target_hours' => 33,
                'additional_role' => 'WALI KELAS, PEMBINA  EKSKUL, PIKET',
                'assignments' => [
                    ['subject' => 'AKIDAH AKHLAK', 'classes' => [
                        ['classroom' => 'X.1', 'hours' => 2],
                        ['classroom' => 'X.2', 'hours' => 2],
                        ['classroom' => 'X.3', 'hours' => 2],
                        ['classroom' => 'X.4', 'hours' => 2],
                        ['classroom' => 'X.5', 'hours' => 2],
                        ['classroom' => 'XI.1', 'hours' => 2],
                        ['classroom' => 'XI.2', 'hours' => 2],
                        ['classroom' => 'XII.1', 'hours' => 2],
                        ['classroom' => 'XII.2', 'hours' => 2],
                        ['classroom' => 'XII.3', 'hours' => 2],
                        ['classroom' => 'XII.4', 'hours' => 2],
                        ['classroom' => 'XII.5', 'hours' => 2],
                    ]],
                ],
            ],
            [
                'order_no' => 10,
                'name' => 'MIFTAHUL JANNAH, S.Pd, M.Si',
                'target_hours' => 33,
                'additional_role' => 'WALI KELAS, PEMBINA  EKSKUL',
                'assignments' => [
                    ['subject' => 'MATEMATIKA WAJIB', 'classes' => [
                        ['classroom' => 'XI.1', 'hours' => 3],
                        ['classroom' => 'XI.2', 'hours' => 3],
                        ['classroom' => 'XI.3', 'hours' => 3],
                        ['classroom' => 'XI.4', 'hours' => 3],
                        ['classroom' => 'XI.5', 'hours' => 3],
                    ]],
                    ['subject' => 'MATEMATIKA TINGKAT LANJUT', 'classes' => [
                        ['classroom' => 'XI.1', 'hours' => 5],
                        ['classroom' => 'XI.2', 'hours' => 5],
                    ]],
                ],
            ],
            [
                'order_no' => 11,
                'name' => 'TEMY YULIANTI, S.Pd., M.Pd.',
                'target_hours' => 27,
                'additional_role' => 'WAKIL KEPALA BIDANG AKADEMIK',
                'assignments' => [
                    ['subject' => 'GEOGRAFI', 'classes' => [
                        ['classroom' => 'X.1', 'hours' => 2],
                        ['classroom' => 'X.2', 'hours' => 2],
                        ['classroom' => 'X.3', 'hours' => 2],
                        ['classroom' => 'X.4', 'hours' => 2],
                        ['classroom' => 'X.5', 'hours' => 2],
                    ]],
                    ['subject' => 'GEOGRAFI TINGKAT LANJUT', 'classes' => [
                        ['classroom' => 'XII.4', 'hours' => 5],
                    ]],
                ],
            ],
            [
                'order_no' => 12,
                'name' => 'IIS SURYANI S.Pd.I.',
                'target_hours' => 27,
                'additional_role' => 'PIKET',
                'assignments' => [
                    ['subject' => 'SKI', 'classes' => [
                        ['classroom' => 'X.3', 'hours' => 2],
                        ['classroom' => 'X.4', 'hours' => 2],
                        ['classroom' => 'X.5', 'hours' => 2],
                        ['classroom' => 'XI.1', 'hours' => 2],
                        ['classroom' => 'XI.2', 'hours' => 2],
                        ['classroom' => 'XI.3', 'hours' => 2],
                        ['classroom' => 'XI.4', 'hours' => 2],
                        ['classroom' => 'XI.5', 'hours' => 2],
                        ['classroom' => 'XII.1', 'hours' => 2],
                        ['classroom' => 'XII.2', 'hours' => 2],
                        ['classroom' => 'XII.3', 'hours' => 2],
                        ['classroom' => 'XII.4', 'hours' => 2],
                        ['classroom' => 'XII.5', 'hours' => 2],
                    ]],
                ],
            ],
            [
                'order_no' => 13,
                'name' => 'IPONG ROHIMAH S.Ag.',
                'target_hours' => 36,
                'additional_role' => 'WALI KELAS',
                'assignments' => [
                    ['subject' => 'FIQIH', 'classes' => [
                        ['classroom' => 'X.1', 'hours' => 2],
                        ['classroom' => 'X.2', 'hours' => 2],
                        ['classroom' => 'X.3', 'hours' => 2],
                        ['classroom' => 'X.4', 'hours' => 2],
                        ['classroom' => 'X.5', 'hours' => 2],
                        ['classroom' => 'XI.1', 'hours' => 2],
                        ['classroom' => 'XI.2', 'hours' => 2],
                        ['classroom' => 'XI.3', 'hours' => 2],
                        ['classroom' => 'XI.4', 'hours' => 2],
                        ['classroom' => 'XI.5', 'hours' => 2],
                        ['classroom' => 'XII.1', 'hours' => 2],
                        ['classroom' => 'XII.2', 'hours' => 2],
                        ['classroom' => 'XII.3', 'hours' => 2],
                        ['classroom' => 'XII.4', 'hours' => 2],
                        ['classroom' => 'XII.5', 'hours' => 2],
                    ]],
                ],
            ],
            [
                'order_no' => 14,
                'name' => 'LINDA AGUNG, S.TP',
                'target_hours' => 31,
                'additional_role' => 'STAF WAKIL KEPALA BIDANG SARPRAS, WALI KELAS',
                'assignments' => [
                    ['subject' => 'FISIKA', 'classes' => [
                        ['classroom' => 'X.1', 'hours' => 2],
                        ['classroom' => 'X.2', 'hours' => 2],
                        ['classroom' => 'X.3', 'hours' => 2],
                        ['classroom' => 'X.4', 'hours' => 2],
                        ['classroom' => 'X.5', 'hours' => 2],
                    ]],
                    ['subject' => 'FISIKA TINGKAT LANJUT', 'classes' => [
                        ['classroom' => 'XI.2', 'hours' => 5],
                        ['classroom' => 'XII.1', 'hours' => 5],
                        ['classroom' => 'XII.2', 'hours' => 5],
                    ]],
                ],
            ],
            [
                'order_no' => 15,
                'name' => 'HASAN BASRI S.Pd, M.Si.',
                'target_hours' => 24,
                'additional_role' => 'PEMBINA  EKSKUL, KOORDINATOR KOKURIKULER',
                'assignments' => [
                    ['subject' => 'EKONOMI', 'classes' => [
                        ['classroom' => 'X.1', 'hours' => 2],
                        ['classroom' => 'X.2', 'hours' => 2],
                        ['classroom' => 'X.3', 'hours' => 2],
                        ['classroom' => 'X.4', 'hours' => 2],
                        ['classroom' => 'X.5', 'hours' => 2],
                    ]],
                    ['subject' => 'KETERAMPILAN', 'classes' => [
                        ['classroom' => 'X.1', 'hours' => 2],
                        ['classroom' => 'X.2', 'hours' => 2],
                        ['classroom' => 'X.3', 'hours' => 2],
                        ['classroom' => 'X.4', 'hours' => 2],
                        ['classroom' => 'X.5', 'hours' => 2],
                    ]],
                ],
            ],
            [
                'order_no' => 16,
                'name' => 'SITI BAROROH, S.Pd.',
                'target_hours' => 29,
                'additional_role' => 'WALI KELAS, PEMBINA  EKSKUL, PIKET',
                'assignments' => [
                    ['subject' => 'EKONOMI TINGKAT LANJUT', 'classes' => [
                        ['classroom' => 'XI.4', 'hours' => 5],
                        ['classroom' => 'XI.5', 'hours' => 5],
                        ['classroom' => 'XII.4', 'hours' => 5],
                        ['classroom' => 'XII.5', 'hours' => 5],
                    ]],
                ],
            ],
            [
                'order_no' => 17,
                'name' => 'OMID SYAMSUDIN S.Ag.',
                'target_hours' => 25,
                'additional_role' => '',
                'assignments' => [
                    ['subject' => 'BAHASA ARAB WAJIB', 'classes' => [
                        ['classroom' => 'X.1', 'hours' => 4],
                        ['classroom' => 'X.2', 'hours' => 4],
                        ['classroom' => 'X.3', 'hours' => 4],
                        ['classroom' => 'X.4', 'hours' => 4],
                        ['classroom' => 'X.5', 'hours' => 4],
                    ]],
                    ['subject' => 'BAHASA ARAB TINGKAT LANJUT', 'classes' => [
                        ['classroom' => 'XI.3', 'hours' => 5],
                    ]],
                ],
            ],
            [
                'order_no' => 18,
                'name' => 'HIDAYAT MUSTOFA, S.Pd.I.',
                'target_hours' => 24,
                'additional_role' => 'WAKIL KEPALA BIDANG SARPRAS',
                'assignments' => [
                    ['subject' => 'QURDIS', 'classes' => [
                        ['classroom' => 'XI.3', 'hours' => 2],
                        ['classroom' => 'XI.4', 'hours' => 2],
                        ['classroom' => 'XI.5', 'hours' => 2],
                    ]],
                    ['subject' => 'AKIDAH AKHLAK', 'classes' => [
                        ['classroom' => 'XI.3', 'hours' => 2],
                        ['classroom' => 'XI.4', 'hours' => 2],
                        ['classroom' => 'XI.5', 'hours' => 2],
                    ]],
                ],
            ],
            [
                'order_no' => 19,
                'name' => 'MIFTAH ELFALAH, SE.',
                'target_hours' => 27,
                'additional_role' => 'WALI KELAS, PIKET',
                'assignments' => [
                    ['subject' => 'KETERAMPILAN', 'classes' => [
                        ['classroom' => 'XI.1', 'hours' => 2],
                        ['classroom' => 'XI.2', 'hours' => 2],
                        ['classroom' => 'XI.3', 'hours' => 2],
                        ['classroom' => 'XI.4', 'hours' => 2],
                        ['classroom' => 'XI.5', 'hours' => 2],
                        ['classroom' => 'XII.1', 'hours' => 2],
                        ['classroom' => 'XII.2', 'hours' => 2],
                        ['classroom' => 'XII.3', 'hours' => 2],
                        ['classroom' => 'XII.4', 'hours' => 2],
                        ['classroom' => 'XII.5', 'hours' => 2],
                    ]],
                ],
            ],
            [
                'order_no' => 20,
                'name' => 'WAHYU UJIANTO, S.S.',
                'target_hours' => 32,
                'additional_role' => 'WAKIL KEPALA BIDANG HUMAS',
                'assignments' => [
                    ['subject' => 'BAHASA INGGRIS TINGKAT LANJUT', 'classes' => [
                        ['classroom' => 'XI.1', 'hours' => 5],
                        ['classroom' => 'XI.3', 'hours' => 5],
                        ['classroom' => 'XII.3', 'hours' => 5],
                        ['classroom' => 'XII.5', 'hours' => 5],
                    ]],
                ],
            ],
            [
                'order_no' => 21,
                'name' => 'ALI SUBHAN, S.Ag.',
                'target_hours' => 31,
                'additional_role' => 'WALI KELAS, PIKET',
                'assignments' => [
                    ['subject' => 'ALQURAN HADIST', 'classes' => [
                        ['classroom' => 'X.1', 'hours' => 2],
                        ['classroom' => 'X.2', 'hours' => 2],
                        ['classroom' => 'X.3', 'hours' => 2],
                        ['classroom' => 'X.4', 'hours' => 2],
                        ['classroom' => 'X.5', 'hours' => 2],
                        ['classroom' => 'XI.1', 'hours' => 2],
                        ['classroom' => 'XI.2', 'hours' => 2],
                        ['classroom' => 'XII.1', 'hours' => 2],
                        ['classroom' => 'XII.2', 'hours' => 2],
                        ['classroom' => 'XII.3', 'hours' => 2],
                        ['classroom' => 'XII.4', 'hours' => 2],
                        ['classroom' => 'XII.5', 'hours' => 2],
                    ]],
                ],
            ],
            [
                'order_no' => 22,
                'name' => 'FERIYA HASTUTI, S.S.',
                'target_hours' => 36,
                'additional_role' => 'WALI KELAS',
                'assignments' => [
                    ['subject' => 'BAHASA INGGRIS WAJIB', 'classes' => [
                        ['classroom' => 'X.1', 'hours' => 2],
                        ['classroom' => 'X.2', 'hours' => 2],
                        ['classroom' => 'X.3', 'hours' => 2],
                        ['classroom' => 'X.4', 'hours' => 2],
                        ['classroom' => 'X.5', 'hours' => 2],
                        ['classroom' => 'XI.1', 'hours' => 2],
                        ['classroom' => 'XI.2', 'hours' => 2],
                        ['classroom' => 'XI.3', 'hours' => 2],
                        ['classroom' => 'XI.4', 'hours' => 2],
                        ['classroom' => 'XI.5', 'hours' => 2],
                        ['classroom' => 'XII.1', 'hours' => 2],
                        ['classroom' => 'XII.2', 'hours' => 2],
                        ['classroom' => 'XII.3', 'hours' => 2],
                        ['classroom' => 'XII.4', 'hours' => 2],
                        ['classroom' => 'XII.5', 'hours' => 2],
                    ]],
                ],
            ],
            [
                'order_no' => 23,
                'name' => 'RIZAL, S.Pd.',
                'target_hours' => 27,
                'additional_role' => 'KEPALA PERPUSTAKAAN',
                'assignments' => [
                    ['subject' => 'MATEMATIKA WAJIB', 'classes' => [
                        ['classroom' => 'XII.1', 'hours' => 3],
                        ['classroom' => 'XII.2', 'hours' => 3],
                        ['classroom' => 'XII.3', 'hours' => 3],
                        ['classroom' => 'XII.4', 'hours' => 3],
                        ['classroom' => 'XII.5', 'hours' => 3],
                    ]],
                ],
            ],
            [
                'order_no' => 24,
                'name' => 'NURHASANAH, S.Pd.',
                'target_hours' => 28,
                'additional_role' => 'STAF WAKIL KEPALA BIDANG AKADEMIK, PEMBINA  EKSKUL, PIKET',
                'assignments' => [
                    ['subject' => 'BK', 'classes' => [
                        ['classroom' => 'XII.1', 'hours' => 1],
                        ['classroom' => 'XII.2', 'hours' => 1],
                        ['classroom' => 'XII.3', 'hours' => 1],
                        ['classroom' => 'XII.4', 'hours' => 1],
                        ['classroom' => 'XII.5', 'hours' => 1],
                    ]],
                ],
            ],
            [
                'order_no' => 25,
                'name' => 'PUTRI DWI LESTARI, S.Pd.',
                'target_hours' => 36,
                'additional_role' => 'STAF WAKIL KEPALA BIDANG KESISWAAN, WALI KELAS',
                'assignments' => [
                    ['subject' => 'SEJARAH WAJIB', 'classes' => [
                        ['classroom' => 'X.1', 'hours' => 2],
                        ['classroom' => 'X.2', 'hours' => 2],
                        ['classroom' => 'X.3', 'hours' => 2],
                        ['classroom' => 'X.4', 'hours' => 2],
                        ['classroom' => 'X.5', 'hours' => 2],
                    ]],
                    ['subject' => 'SEJARAH TINGKAT LANJUT', 'classes' => [
                        ['classroom' => 'XI.4', 'hours' => 5],
                        ['classroom' => 'XI.5', 'hours' => 5],
                        ['classroom' => 'XII.4', 'hours' => 5],
                        ['classroom' => 'XII.5', 'hours' => 5],
                    ]],
                ],
            ],
            [
                'order_no' => 26,
                'name' => 'MUHAMMAD SUWANDI, S.Pd.',
                'target_hours' => 24,
                'additional_role' => 'WAKIL KEPALA BIDANG KESISWAAN',
                'assignments' => [
                    ['subject' => 'BAHASA INDONESIA WAJIB', 'classes' => [
                        ['classroom' => 'XII.1', 'hours' => 3],
                        ['classroom' => 'XII.2', 'hours' => 3],
                        ['classroom' => 'XII.3', 'hours' => 3],
                    ]],
                    ['subject' => 'BAHASA INDONESIA TINGKAT LANJUT', 'classes' => [
                        ['classroom' => 'XI.3', 'hours' => 3],
                    ]],
                ],
            ],
            [
                'order_no' => 27,
                'name' => 'LEYLIA ARUM AMALUN NISA, S.Pd.',
                'target_hours' => 32,
                'additional_role' => 'WALI KELAS, PEMBINA  EKSKUL, PIKET',
                'assignments' => [
                    ['subject' => 'BAHASA INDONESIA WAJIB', 'classes' => [
                        ['classroom' => 'XI.1', 'hours' => 3],
                        ['classroom' => 'XI.2', 'hours' => 3],
                        ['classroom' => 'XI.3', 'hours' => 3],
                        ['classroom' => 'XI.4', 'hours' => 3],
                        ['classroom' => 'XI.5', 'hours' => 3],
                        ['classroom' => 'XII.4', 'hours' => 3],
                        ['classroom' => 'XII.5', 'hours' => 3],
                    ]],
                    ['subject' => 'BAHASA INDONESIA TINGKAT LANJUT', 'classes' => [
                        ['classroom' => 'XI.3', 'hours' => 2],
                    ]],
                ],
            ],
            [
                'order_no' => 28,
                'name' => 'ARIEL BONARDO H. LIMBONG, S.Pd.',
                'target_hours' => 31,
                'additional_role' => 'KEPALA LAB BAHASA, PEMBINA  EKSKUL, PIKET',
                'assignments' => [
                    ['subject' => 'PJOK', 'classes' => [
                        ['classroom' => 'XI.3', 'hours' => 2],
                        ['classroom' => 'XI.4', 'hours' => 2],
                        ['classroom' => 'XI.5', 'hours' => 2],
                        ['classroom' => 'XII.1', 'hours' => 2],
                        ['classroom' => 'XII.2', 'hours' => 2],
                        ['classroom' => 'XII.3', 'hours' => 2],
                        ['classroom' => 'XII.4', 'hours' => 2],
                        ['classroom' => 'XII.5', 'hours' => 2],
                    ]],
                ],
            ],
            [
                'order_no' => 29,
                'name' => 'CITRA ARIYANTI, S.Sos.',
                'target_hours' => 28,
                'additional_role' => 'PEMBINA  EKSKUL, PIKET',
                'assignments' => [
                    ['subject' => 'BK', 'classes' => [
                        ['classroom' => 'X.1', 'hours' => 1],
                        ['classroom' => 'X.2', 'hours' => 1],
                        ['classroom' => 'X.3', 'hours' => 1],
                        ['classroom' => 'X.4', 'hours' => 1],
                        ['classroom' => 'X.5', 'hours' => 1],
                    ]],
                ],
            ],
            [
                'order_no' => 30,
                'name' => 'HANNY MILLENIA S.Pd',
                'target_hours' => 34,
                'additional_role' => 'WALI KELAS, PEMBINA  EKSKUL, PIKET',
                'assignments' => [
                    ['subject' => 'PENDIDIKAN PANCASILA', 'classes' => [
                        ['classroom' => 'X.1', 'hours' => 2],
                        ['classroom' => 'X.2', 'hours' => 2],
                        ['classroom' => 'X.3', 'hours' => 2],
                        ['classroom' => 'X.4', 'hours' => 2],
                        ['classroom' => 'X.5', 'hours' => 2],
                        ['classroom' => 'XII.1', 'hours' => 2],
                        ['classroom' => 'XII.2', 'hours' => 2],
                        ['classroom' => 'XII.3', 'hours' => 2],
                        ['classroom' => 'XII.4', 'hours' => 2],
                        ['classroom' => 'XII.5', 'hours' => 2],
                    ]],
                    ['subject' => 'GEOGRAFI TINGKAT LANJUT', 'classes' => [
                        ['classroom' => 'XI.4', 'hours' => 5],
                    ]],
                ],
            ],
            [
                'order_no' => 31,
                'name' => 'LINTANG ADI RAMADHAN, S.Pd.',
                'target_hours' => 32,
                'additional_role' => 'PEMBINA  EKSKUL',
                'assignments' => [
                    ['subject' => 'SBK', 'classes' => [
                        ['classroom' => 'X.1', 'hours' => 2],
                        ['classroom' => 'X.2', 'hours' => 2],
                        ['classroom' => 'X.3', 'hours' => 2],
                        ['classroom' => 'X.4', 'hours' => 2],
                        ['classroom' => 'X.5', 'hours' => 2],
                        ['classroom' => 'XI.1', 'hours' => 2],
                        ['classroom' => 'XI.2', 'hours' => 2],
                        ['classroom' => 'XI.3', 'hours' => 2],
                        ['classroom' => 'XI.4', 'hours' => 2],
                        ['classroom' => 'XI.5', 'hours' => 2],
                        ['classroom' => 'XII.1', 'hours' => 2],
                        ['classroom' => 'XII.2', 'hours' => 2],
                        ['classroom' => 'XII.3', 'hours' => 2],
                        ['classroom' => 'XII.4', 'hours' => 2],
                        ['classroom' => 'XII.5', 'hours' => 2],
                    ]],
                ],
            ],
            [
                'order_no' => 32,
                'name' => 'M. IKHLAS, S.Pd.',
                'target_hours' => 29,
                'additional_role' => 'STAF WAKA BIDANG HUMAS, WALI KELAS, PEMBINA  EKSKUL, PIKET',
                'assignments' => [
                    ['subject' => 'BIOLOGI', 'classes' => [
                        ['classroom' => 'X.1', 'hours' => 2],
                        ['classroom' => 'X.2', 'hours' => 2],
                        ['classroom' => 'X.3', 'hours' => 2],
                        ['classroom' => 'X.4', 'hours' => 2],
                        ['classroom' => 'X.5', 'hours' => 2],
                    ]],
                    ['subject' => 'BIOLOGI TINGKAT LANJUT', 'classes' => [
                        ['classroom' => 'XI.1', 'hours' => 5],
                        ['classroom' => 'XI.2', 'hours' => 5],
                    ]],
                ],
            ],
            [
                'order_no' => 33,
                'name' => 'DIKA SAVITRI, S.Pd.',
                'target_hours' => 30,
                'additional_role' => '',
                'assignments' => [
                    ['subject' => 'PENDIDIKAN PANCASILA', 'classes' => [
                        ['classroom' => 'XI.1', 'hours' => 2],
                        ['classroom' => 'XI.2', 'hours' => 2],
                        ['classroom' => 'XI.3', 'hours' => 2],
                        ['classroom' => 'XI.4', 'hours' => 2],
                        ['classroom' => 'XI.5', 'hours' => 2],
                    ]],
                    ['subject' => 'SEJARAH INDONESIA', 'classes' => [
                        ['classroom' => 'XI.1', 'hours' => 2],
                        ['classroom' => 'XI.2', 'hours' => 2],
                        ['classroom' => 'XI.3', 'hours' => 2],
                        ['classroom' => 'XI.4', 'hours' => 2],
                        ['classroom' => 'XI.5', 'hours' => 2],
                        ['classroom' => 'XII.1', 'hours' => 2],
                        ['classroom' => 'XII.2', 'hours' => 2],
                        ['classroom' => 'XII.3', 'hours' => 2],
                        ['classroom' => 'XII.4', 'hours' => 2],
                        ['classroom' => 'XII.5', 'hours' => 2],
                    ]],
                ],
            ],
            [
                'order_no' => 34,
                'name' => 'DANDY ARIF RINALDI, S.Pd.',
                'target_hours' => 21,
                'additional_role' => 'PEMBINA  EKSKUL',
                'assignments' => [
                    ['subject' => 'PJOK', 'classes' => [
                        ['classroom' => 'X.1', 'hours' => 3],
                        ['classroom' => 'X.2', 'hours' => 3],
                        ['classroom' => 'X.3', 'hours' => 3],
                        ['classroom' => 'X.4', 'hours' => 3],
                        ['classroom' => 'X.5', 'hours' => 3],
                        ['classroom' => 'XI.1', 'hours' => 2],
                        ['classroom' => 'XI.2', 'hours' => 2],
                        ['classroom' => 'XI.3', 'hours' => 2],
                    ]],
                ],
            ],
            [
                'order_no' => 35,
                'name' => 'MUHAMAD RICO SAPUTRA, S.Pd.',
                'target_hours' => 23,
                'additional_role' => 'PEMBINA  OSIS, PIKET',
                'assignments' => [
                    ['subject' => 'HIFZIL QURAN', 'classes' => [
                        ['classroom' => 'X.1', 'hours' => 2],
                        ['classroom' => 'X.2', 'hours' => 2],
                        ['classroom' => 'X.3', 'hours' => 2],
                        ['classroom' => 'X.4', 'hours' => 2],
                        ['classroom' => 'X.5', 'hours' => 2],
                        ['classroom' => 'XI.1', 'hours' => 1],
                        ['classroom' => 'XI.2', 'hours' => 1],
                        ['classroom' => 'XI.3', 'hours' => 1],
                        ['classroom' => 'XI.4', 'hours' => 1],
                        ['classroom' => 'XI.5', 'hours' => 1],
                        ['classroom' => 'XII.1', 'hours' => 1],
                        ['classroom' => 'XII.2', 'hours' => 1],
                        ['classroom' => 'XII.3', 'hours' => 1],
                        ['classroom' => 'XII.4', 'hours' => 1],
                        ['classroom' => 'XII.5', 'hours' => 1],
                    ]],
                ],
            ],
            [
                'order_no' => 36,
                'name' => 'DIEGO WAHYU RAMADHAN, S.Kom.',
                'target_hours' => 32,
                'additional_role' => 'KEPALA LAB KOMPUTER',
                'assignments' => [
                    ['subject' => 'INFORMATIKA', 'classes' => [
                        ['classroom' => 'X.1', 'hours' => 2],
                        ['classroom' => 'X.2', 'hours' => 2],
                        ['classroom' => 'X.3', 'hours' => 2],
                        ['classroom' => 'X.4', 'hours' => 2],
                        ['classroom' => 'X.5', 'hours' => 2],
                    ]],
                    ['subject' => 'INFORMATIKA TINGKAT LANJUT', 'classes' => [
                        ['classroom' => 'XII.1', 'hours' => 5],
                        ['classroom' => 'XII.3', 'hours' => 5],
                    ]],
                ],
            ],
        ];

                // Bersihkan data penugasan & jadwal agar bersih 100%
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schedule::truncate();
        TeacherAssignment::truncate();
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        foreach ($teachersData as $tData) {
            $teacher = Teacher::updateOrCreate(
                ['name' => $tData['name']],
                [
                    'order_no' => $tData['order_no'],
                    'target_hours' => $tData['target_hours'],
                    'additional_role' => $tData['additional_role'],
                    'color' => '#' . substr(md5($tData['name']), 0, 6),
                ]
            );

            foreach ($tData['assignments'] as $assign) {
                $subjectId = $subjectMap[$assign['subject']] ?? null;
                if ($subjectId) {
                    foreach ($assign['classes'] as $cls) {
                        $classId = $classroomMap[$cls['classroom']] ?? null;
                        if ($classId) {
                            TeacherAssignment::updateOrCreate(
                                [
                                    'teacher_id' => $teacher->id,
                                    'subject_id' => $subjectId,
                                    'classroom_id' => $classId,
                                ],
                                [
                                    'hours_per_week' => $cls['hours'],
                                ]
                            );
                        }
                    }
                }
            }
        }

        // 7. Pemetaan Wali Kelas (Homeroom Teachers) Resmi MAN 20 Jakarta Sesuai SK
        $walasMapping = [
            'X.1' => 'LINDA AGUNG, S.TP',
            'X.2' => 'SITI RUWAIDAH, S.Pd.',
            'X.3' => 'M. IKHLAS, S.Pd.',
            'X.4' => 'PUTRI DWI LESTARI, S.Pd.',
            'X.5' => 'IPONG ROHIMAH S.Ag.',
            'XI.1' => 'MIFTAH ELFALAH, SE.',
            'XI.2' => 'MIFTAHUL JANNAH, S.Pd, M.Si',
            'XI.3' => 'LEYLIA ARUM AMALUN NISA, S.Pd.',
            'XI.4' => 'HANNY MILLENIA S.Pd',
            'XI.5' => 'FERIYA HASTUTI, S.S.',
            'XII.1' => 'ALI SUBHAN, S.Ag.',
            'XII.2' => 'SITI HASANAH, S.Ag.',
            'XII.3' => 'FIRMANSYAH, S.Ag.,M.Hum.',
            'XII.4' => 'DENI SUTEDI, MM.Pd.',
            'XII.5' => 'SITI BAROROH, S.Pd.',
        ];

        foreach ($walasMapping as $className => $teacherName) {
            $walasTeacher = Teacher::where('name', $teacherName)->first();
            if ($walasTeacher) {
                Classroom::where('name', $className)->update([
                    'homeroom_teacher_id' => $walasTeacher->id,
                ]);
            }
        }

                // 8. Seeding Jadwal Pelajaran Resmi MAN 20 Jakarta (Sesuai SK Revisi 4)
        $officialSchedule = OfficialScheduleData::getDaysSchedule();
        $pramukaSubject = Subject::where('name', 'Pramuka Wajib')->first();
        $pramukaTeacher = Teacher::where('name', 'ARIEL BONARDO H. LIMBONG, S.Pd.')->first();

        foreach ($officialSchedule as $day => $dInfo) {
            foreach ($dInfo['periods'] as $pNum => $pData) {
                if (!isset($pData['slots'])) continue;

                $timeSlot = TimeSlot::where('day', $day)->where('period', $pNum)->first();
                if (!$timeSlot) continue;

                foreach ($pData['slots'] as $clsCode => $slot) {
                    $clsName = str_replace('-', '.', $clsCode);
                    $classroom = Classroom::where('name', $clsName)->first();
                    if (!$classroom) continue;

                    if ($slot === 'PRAMUKA') {
                        if ($pramukaSubject && $pramukaTeacher) {
                            $pramukaAssign = TeacherAssignment::firstOrCreate(
                                [
                                    'teacher_id' => $pramukaTeacher->id,
                                    'subject_id' => $pramukaSubject->id,
                                    'classroom_id' => $classroom->id,
                                ],
                                ['hours_per_week' => 2]
                            );
                            Schedule::updateOrCreate(
                                [
                                    'classroom_id' => $classroom->id,
                                    'time_slot_id' => $timeSlot->id,
                                ],
                                [
                                    'teacher_id' => $pramukaTeacher->id,
                                    'subject_id' => $pramukaSubject->id,
                                    'teacher_assignment_id' => $pramukaAssign->id,
                                    'day' => $day,
                                    'period' => $pNum,
                                ]
                            );
                        }
                        continue;
                    }

                    preg_match('/^(\d+)([A-Z]?)$/', $slot, $m);
                    $orderNo = (int)($m[1] ?? 0);
                    $suffix = $m[2] ?? '';

                    $teacher = Teacher::where('order_no', $orderNo)->first();
                    if (!$teacher) continue;

                    $assigns = TeacherAssignment::where('teacher_id', $teacher->id)
                        ->where('classroom_id', $classroom->id)
                        ->with('subject')
                        ->get();

                    $chosenAssign = null;
                    if ($assigns->count() === 1) {
                        $chosenAssign = $assigns->first();
                    } elseif ($assigns->count() > 1) {
                        if ($orderNo === 15) {
                            $chosenAssign = $assigns->first(function($a) use ($suffix) {
                                return $suffix === 'E' ? str_contains($a->subject->name, 'EKONOMI') : str_contains($a->subject->name, 'KETERAMPILAN');
                            });
                        } elseif ($orderNo === 17) {
                            $chosenAssign = $assigns->first(function($a) use ($suffix) {
                                return $suffix === 'T' ? str_contains($a->subject->name, 'TINGKAT LANJUT') : !str_contains($a->subject->name, 'TINGKAT LANJUT');
                            });
                        } elseif ($orderNo === 18) {
                            $chosenAssign = $assigns->first(function($a) use ($suffix) {
                                return $suffix === 'A' ? str_contains($a->subject->name, 'AKIDAH') : (str_contains($a->subject->name, 'QUR') || str_contains($a->subject->name, 'HADIST'));
                            });
                        } elseif ($orderNo === 30) {
                            $chosenAssign = $assigns->first(function($a) use ($suffix) {
                                return $suffix === 'G' ? str_contains($a->subject->name, 'GEOGRAFI') : str_contains($a->subject->name, 'PANCASILA');
                            });
                        } elseif ($orderNo === 33) {
                            $chosenAssign = $assigns->first(function($a) use ($suffix) {
                                return $suffix === 'S' ? str_contains($a->subject->name, 'SEJARAH') : str_contains($a->subject->name, 'PANCASILA');
                            });
                        } else {
                            $chosenAssign = $assigns->first();
                        }
                    }

                    if ($chosenAssign) {
                        Schedule::updateOrCreate(
                            [
                                'classroom_id' => $classroom->id,
                                'time_slot_id' => $timeSlot->id,
                            ],
                            [
                                'teacher_id' => $teacher->id,
                                'subject_id' => $chosenAssign->subject_id,
                                'teacher_assignment_id' => $chosenAssign->id,
                                'day' => $day,
                                'period' => $pNum,
                            ]
                        );
                    }
                }
            }
        }
    }
}
