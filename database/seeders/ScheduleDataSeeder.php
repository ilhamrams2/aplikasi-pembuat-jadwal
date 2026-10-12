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
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;

class ScheduleDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Admin Users
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

        // 2. School Settings
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

        // 3. Classrooms (15 Rombel)
        $classes = [
            'X.1' => 10, 'X.2' => 10, 'X.3' => 10, 'X.4' => 10, 'X.5' => 10,
            'XI.1' => 11, 'XI.2' => 11, 'XI.3' => 11, 'XI.4' => 11, 'XI.5' => 11,
            'XII.1' => 12, 'XII.2' => 12, 'XII.3' => 12, 'XII.4' => 12, 'XII.5' => 12,
        ];

        $classroomMap = [];
        foreach ($classes as $name => $grade) {
            $c = Classroom::updateOrCreate(
                ['name' => $name],
                ['grade' => $grade]
            );
            $classroomMap[$name] = $c->id;
        }

        // 4. Time Slots
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        $periods = [
            ['period' => 1, 'start' => '06:45', 'end' => '07:30', 'is_break' => false, 'break_name' => null],
            ['period' => 2, 'start' => '07:30', 'end' => '08:15', 'is_break' => false, 'break_name' => null],
            ['period' => 3, 'start' => '08:15', 'end' => '09:00', 'is_break' => false, 'break_name' => null],
            ['period' => 4, 'start' => '09:00', 'end' => '09:45', 'is_break' => false, 'break_name' => null],
            ['period' => 5, 'start' => '10:15', 'end' => '11:00', 'is_break' => false, 'break_name' => null],
            ['period' => 6, 'start' => '11:00', 'end' => '11:45', 'is_break' => false, 'break_name' => null],
            ['period' => 7, 'start' => '11:45', 'end' => '12:30', 'is_break' => false, 'break_name' => null],
            ['period' => 8, 'start' => '13:00', 'end' => '13:45', 'is_break' => false, 'break_name' => null],
            ['period' => 9, 'start' => '13:45', 'end' => '14:30', 'is_break' => false, 'break_name' => null],
            ['period' => 10, 'start' => '14:30', 'end' => '15:15', 'is_break' => false, 'break_name' => null],
        ];

        foreach ($days as $day) {
            foreach ($periods as $p) {
                TimeSlot::updateOrCreate(
                    ['day' => $day, 'period' => $p['period']],
                    [
                        'start_time' => $p['start'],
                        'end_time' => $p['end'],
                        'is_break' => $p['is_break'],
                        'break_name' => $p['break_name'],
                    ]
                );
            }
        }

        // 5. Load JSON Seed Data
        $jsonPath = base_path('scratch_seed_data.json');
        if (File::exists($jsonPath)) {
            $data = json_decode(File::get($jsonPath), true);

            // Seed Subjects
            $subjectMap = [];
            foreach ($data['subjects'] as $sName => $sInfo) {
                $sub = Subject::updateOrCreate(
                    ['name' => $sName],
                    [
                        'code' => $sInfo['code'],
                        'category' => $sInfo['category'],
                        'color' => $sInfo['color'],
                    ]
                );
                $subjectMap[$sName] = $sub->id;
            }

            // Also ensure 'Pramuka Wajib' exists for Kelas X
            $pramuka = Subject::updateOrCreate(
                ['name' => 'Pramuka Wajib'],
                ['code' => 'PRAM', 'category' => 'Wajib', 'color' => '#854D0E']
            );
            $subjectMap['Pramuka Wajib'] = $pramuka->id;

            // Seed Teachers & Assignments
            foreach ($data['teachers'] as $tData) {
                $teacher = Teacher::updateOrCreate(
                    ['name' => $tData['name']],
                    [
                        'order_no' => $tData['order_no'],
                        'target_hours' => $tData['target_hours'],
                        'additional_role' => implode(', ', $tData['additional_roles']),
                        'color' => '#' . substr(md5($tData['name']), 0, 6),
                    ]
                );

                foreach ($tData['assignments'] as $assign) {
                    $sName = $assign['subject'];
                    $subjectId = $subjectMap[$sName] ?? null;

                    if ($subjectId) {
                        foreach ($assign['classes'] as $cls) {
                            $cId = $classroomMap[$cls['classroom']] ?? null;
                            if ($cId) {
                                TeacherAssignment::updateOrCreate(
                                    [
                                        'teacher_id' => $teacher->id,
                                        'subject_id' => $subjectId,
                                        'classroom_id' => $cId,
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
        }
    }
}
