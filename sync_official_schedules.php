<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Teacher;
use App\Models\Classroom;
use App\Models\Subject;
use App\Models\TimeSlot;
use App\Models\TeacherAssignment;
use App\Models\Schedule;
use App\Services\OfficialScheduleData;

echo "--- STARTING OFFICIAL SCHEDULE SYNC ---\n";

// 1. Sync Teachers order_no 1 to 36
$teachersList = OfficialScheduleData::getTeachersList();

foreach ($teachersList as $code => $info) {
    // Search teacher by name similarity
    $teacher = Teacher::where('name', 'LIKE', '%' . explode(' ', $info['name'])[0] . '%')
        ->where('name', 'LIKE', '%' . explode(' ', $info['name'])[1] . '%')
        ->first();

    if (!$teacher) {
        // Fallback search
        $teacher = Teacher::where('order_no', $code)->first();
    }

    if ($teacher) {
        $teacher->order_no = $code;
        $teacher->save();
        echo "Teacher {$code}: {$teacher->name} updated to order_no {$code}\n";
    } else {
        echo "Teacher not found for code {$code}: {$info['name']}\n";
    }
}

// 2. Sync Homeroom Teachers for Classrooms
$homeroom = OfficialScheduleData::getHomeroomTeachers();
foreach ($homeroom as $idx => $wk) {
    $className = str_replace('-', '.', $wk['class']); // 'X-1' -> 'X.1'
    $classroom = Classroom::where('name', $className)->orWhere('name', $wk['class'])->first();
    if ($classroom) {
        $teacher = Teacher::where('name', 'LIKE', '%' . explode(' ', $wk['name'])[0] . '%')
            ->where('name', 'LIKE', '%' . explode(' ', $wk['name'])[1] . '%')
            ->first();
        if ($teacher) {
            $classroom->homeroom_teacher_id = $teacher->id;
            $classroom->save();
            echo "Classroom {$classroom->name} homeroom assigned to {$teacher->name}\n";
        }
    }
}

// 3. Populate Schedules
echo "\nPopulating Schedules...\n";
Schedule::query()->delete();

// Subject mapping by code pattern
$subjectCodeMap = [
    'PRAMUKA' => 'Pramuka Wajib',
    '1' => 'SKI',
    '2' => 'BIOLOGI TINGKAT LANJUT',
    '3' => 'SOSIOLOGI',
    '4' => 'BAHASA INDONESIA WAJIB',
    '5' => 'BK',
    '6' => 'MATEMATIKA WAJIB',
    '7' => 'KIMIA',
    '8' => 'BAHASA ARAB WAJIB',
    '9' => 'AKIDAH AKHLAK',
    '10' => 'MATEMATIKA WAJIB',
    '11' => 'GEOGRAFI',
    '12' => 'SKI',
    '13' => 'FIQIH',
    '14' => 'FISIKA',
    '15' => 'EKONOMI',
    '15E' => 'EKONOMI',
    '15P' => 'KETERAMPILAN',
    '16' => 'EKONOMI TINGKAT LANJUT',
    '17' => 'BAHASA ARAB WAJIB',
    '17T' => 'BAHASA ARAB TINGKAT LANJUT',
    '18' => 'ALQURAN HADIST',
    '18A' => 'AKIDAH AKHLAK',
    '18Q' => 'ALQURAN HADIST',
    '19' => 'KETERAMPILAN',
    '20' => 'BAHASA INGGRIS TINGKAT LANJUT',
    '21' => 'ALQURAN HADIST',
    '22' => 'BAHASA INGGRIS WAJIB',
    '23' => 'MATEMATIKA WAJIB',
    '24' => 'BK',
    '25' => 'SEJARAH WAJIB',
    '26' => 'BAHASA INDONESIA WAJIB',
    '27' => 'BAHASA INDONESIA WAJIB',
    '28' => 'PJOK',
    '29' => 'SOSIOLOGI',
    '30' => 'PENDIDIKAN PANCASILA',
    '30P' => 'PENDIDIKAN PANCASILA',
    '30G' => 'GEOGRAFI TINGKAT LANJUT',
    '31' => 'BAHASA INGGRIS WAJIB',
    '32' => 'BIOLOGI',
    '33' => 'PENDIDIKAN PANCASILA',
    '33P' => 'PENDIDIKAN PANCASILA',
    '33S' => 'SEJARAH INDONESIA',
    '34' => 'PJOK',
    '35' => 'HIFZIL QURAN',
    '36' => 'INFORMATIKA',
];

$daysSchedule = OfficialScheduleData::getDaysSchedule();
$inserted = 0;

foreach ($daysSchedule as $day => $dayInfo) {
    foreach ($dayInfo['periods'] as $periodNum => $pData) {
        if (!is_int($periodNum)) continue; // skip breaks

        $timeSlot = TimeSlot::where('day', $day)->where('period', $periodNum)->first();
        if (!$timeSlot) {
            $timeSlot = TimeSlot::create([
                'day' => $day,
                'period' => $periodNum,
                'start_time' => explode(' - ', $pData['time'])[0] ?? '07:00',
                'end_time' => explode(' - ', $pData['time'])[1] ?? '07:45',
                'is_break' => false
            ]);
        }

        foreach ($pData['slots'] as $classCode => $cellCode) {
            $className = str_replace('-', '.', $classCode); // 'X-1' -> 'X.1'
            $classroom = Classroom::where('name', $className)->orWhere('name', $classCode)->first();
            if (!$classroom) continue;

            // Extract teacher order_no
            $cellClean = trim($cellCode);
            preg_match('/^(\d+)/', $cellClean, $matches);
            $teacherOrderNo = isset($matches[1]) ? (int)$matches[1] : null;

            $teacher = null;
            if ($teacherOrderNo) {
                $teacher = Teacher::where('order_no', $teacherOrderNo)->first();
            }

            // Find subject
            $subjName = $subjectCodeMap[$cellClean] ?? ($subjectCodeMap[$teacherOrderNo] ?? null);
            $subject = null;
            if ($subjName) {
                $subject = Subject::where('name', 'LIKE', '%' . $subjName . '%')->first();
            }
            if (!$subject) {
                $subject = Subject::first();
            }

            if (!$teacher) {
                // If cell is PRAMUKA or special
                $teacher = Teacher::where('order_no', 34)->first() ?: Teacher::first();
            }

            // Find or create TeacherAssignment
            $assignment = TeacherAssignment::firstOrCreate(
                [
                    'classroom_id' => $classroom->id,
                    'teacher_id' => $teacher->id,
                    'subject_id' => $subject->id,
                ],
                [
                    'hours_per_week' => 2,
                ]
            );

            Schedule::create([
                'classroom_id' => $classroom->id,
                'teacher_assignment_id' => $assignment->id,
                'teacher_id' => $teacher->id,
                'subject_id' => $subject->id,
                'time_slot_id' => $timeSlot->id,
                'day' => $day,
                'period' => $periodNum,
                'room' => 'R.' . $classroom->name,
            ]);
            $inserted++;
        }
    }
}

echo "SUCCESS! Inserted {$inserted} official schedule slots into database.\n";
