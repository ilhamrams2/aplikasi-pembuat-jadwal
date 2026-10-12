<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Schedule;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\TeacherAssignment;
use App\Models\TimeSlot;
use App\Models\SchoolSetting;
use App\Services\ScheduleGeneratorService;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    protected $generator;

    public function __construct(ScheduleGeneratorService $generator)
    {
        $this->generator = $generator;
    }

    public function index(Request $request)
    {
        $viewType = $request->get('type', 'class'); // 'class', 'teacher', 'master'
        $selectedClassId = $request->get('classroom_id');
        $selectedTeacherId = $request->get('teacher_id');

        $classrooms = Classroom::orderBy('grade')->orderBy('name')->get();
        $teachers = Teacher::orderBy('name')->get();
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        $periods = range(1, 10);

        if (!$selectedClassId && $classrooms->isNotEmpty()) {
            $selectedClassId = $classrooms->first()->id;
        }
        if (!$selectedTeacherId && $teachers->isNotEmpty()) {
            $selectedTeacherId = $teachers->first()->id;
        }

        // Get schedules depending on viewType
        $schedulesGrid = [];
        $selectedEntity = null;

        if ($viewType === 'class' && $selectedClassId) {
            $selectedEntity = Classroom::with('homeroomTeacher')->find($selectedClassId);
            $schedules = Schedule::where('classroom_id', $selectedClassId)
                ->with('teacher', 'subject', 'timeSlot')
                ->get();

            foreach ($schedules as $s) {
                $schedulesGrid[$s->day][$s->period] = $s;
            }
        } elseif ($viewType === 'teacher' && $selectedTeacherId) {
            $selectedEntity = Teacher::find($selectedTeacherId);
            $schedules = Schedule::where('teacher_id', $selectedTeacherId)
                ->with('classroom', 'subject', 'timeSlot')
                ->get();

            foreach ($schedules as $s) {
                $schedulesGrid[$s->day][$s->period] = $s;
            }
        } elseif ($viewType === 'master') {
            // Master matrix: for a specific day or all
            $selectedDay = $request->get('day', 'Senin');
            $schedules = Schedule::where('day', $selectedDay)
                ->with('classroom', 'teacher', 'subject')
                ->get();

            $masterGrid = [];
            foreach ($schedules as $s) {
                $masterGrid[$s->classroom_id][$s->period] = $s;
            }

            return view('schedules.master', compact(
                'viewType', 'classrooms', 'teachers', 'days', 'periods', 'selectedDay', 'masterGrid'
            ));
        }

        // Available assignments for manual assignment modal
        $availableAssignments = [];
        $teacherBusyMap = [];
        if ($selectedClassId) {
            $availableAssignments = TeacherAssignment::where('classroom_id', $selectedClassId)
                ->with('teacher', 'subject')
                ->withCount('schedules')
                ->get();

            // Map busy slots: key = "teacherId_day_period", value = classroom name
            $allSchedules = Schedule::with('classroom')->get();
            foreach ($allSchedules as $s) {
                if ($s->classroom_id != $selectedClassId) {
                    $teacherBusyMap[$s->teacher_id . '_' . $s->day . '_' . $s->period] = $s->classroom->name;
                }
            }
        }

        $conflicts = $this->generator->detectConflicts();

        return view('schedules.index', compact(
            'viewType', 'classrooms', 'teachers', 'days', 'periods',
            'selectedClassId', 'selectedTeacherId', 'selectedEntity',
            'schedulesGrid', 'availableAssignments', 'conflicts', 'teacherBusyMap'
        ));
    }

    public function generate(Request $request)
    {
        $result = $this->generator->generateAll();

        if ($result['success']) {
            $msg = "Berhasil membuat jadwal otomatis! Total {$result['total_placed']} jam pelajaran berhasil ditempatkan.";
            if (count($result['unplaced']) > 0) {
                $msg .= " Catatan: Terdapat " . count($result['unplaced']) . " jam yang belum termuat karena keterbatasan slot.";
            }
            return back()->with('success', $msg);
        }

        return back()->with('error', 'Gagal membuat jadwal otomatis.');
    }

    public function clear()
    {
        Schedule::query()->delete();
        return back()->with('success', 'Semua data jadwal berhasil direset/dikosongkan.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'classroom_id' => 'required|exists:classrooms,id',
            'teacher_assignment_id' => 'required|exists:teacher_assignments,id',
            'day' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat',
            'period' => 'required|integer|min:1|max:10',
        ]);

        $assignment = TeacherAssignment::findOrFail($request->teacher_assignment_id);

        // Find or create matching time slot
        $timeSlot = TimeSlot::where('day', $request->day)
            ->where('period', $request->period)
            ->first();

        if (!$timeSlot) {
            return back()->with('error', 'Slot waktu tidak ditemukan.');
        }

        // Check if teacher is already busy at this time
        $clash = Schedule::where('teacher_id', $assignment->teacher_id)
            ->where('day', $request->day)
            ->where('period', $request->period)
            ->where('classroom_id', '!=', $request->classroom_id)
            ->with('classroom')
            ->first();

        if ($clash) {
            return back()->with('error', "Bentrok! Guru {$assignment->teacher->name} sudah mengajar di kelas {$clash->classroom->name} pada hari {$request->day} jam ke-{$request->period}.");
        }

        Schedule::updateOrCreate(
            [
                'classroom_id' => $request->classroom_id,
                'time_slot_id' => $timeSlot->id,
            ],
            [
                'teacher_assignment_id' => $assignment->id,
                'teacher_id' => $assignment->teacher_id,
                'subject_id' => $assignment->subject_id,
                'day' => $request->day,
                'period' => $request->period,
                'room' => 'R.' . $assignment->classroom->name,
            ]
        );

        return back()->with('success', 'Jadwal pelajaran berhasil ditambahkan/diperbarui.');
    }

    public function destroy($id)
    {
        $schedule = Schedule::findOrFail($id);
        $schedule->delete();

        return back()->with('success', 'Jadwal pada slot tersebut telah dihapus.');
    }

    public function printClass($id)
    {
        $classroom = Classroom::with('homeroomTeacher')->findOrFail($id);
        $schedules = Schedule::where('classroom_id', $id)->with('teacher', 'subject')->get();

        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        $periods = range(1, 10);
        $schedulesGrid = [];
        foreach ($schedules as $s) {
            $schedulesGrid[$s->day][$s->period] = $s;
        }

        $timeSlots = TimeSlot::orderBy('period')->get()->unique('period')->keyBy('period');
        $settings = SchoolSetting::pluck('value', 'key');

        return view('schedules.print_class', compact('classroom', 'days', 'periods', 'schedulesGrid', 'timeSlots', 'settings'));
    }

    public function printTeacher($id)
    {
        $teacher = Teacher::findOrFail($id);
        $schedules = Schedule::where('teacher_id', $id)->with('classroom', 'subject')->get();

        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        $periods = range(1, 10);
        $schedulesGrid = [];
        foreach ($schedules as $s) {
            $schedulesGrid[$s->day][$s->period] = $s;
        }

        $timeSlots = TimeSlot::orderBy('period')->get()->unique('period')->keyBy('period');
        $settings = SchoolSetting::pluck('value', 'key');

        return view('schedules.print_teacher', compact('teacher', 'days', 'periods', 'schedulesGrid', 'timeSlots', 'settings'));
    }

    public function printAll()
    {
        $classrooms = Classroom::orderBy('grade')->orderBy('name')->get();
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        $periods = range(1, 10);
        $allSchedules = Schedule::with('teacher', 'subject', 'classroom')->get();

        $matrix = [];
        foreach ($allSchedules as $s) {
            $matrix[$s->day][$s->classroom_id][$s->period] = $s;
        }

        $settings = SchoolSetting::pluck('value', 'key');

        return view('schedules.print_all', compact('classrooms', 'days', 'periods', 'matrix', 'settings'));
    }
}
