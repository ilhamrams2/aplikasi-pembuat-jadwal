<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\Subject;
use App\Models\Classroom;
use App\Models\TeacherAssignment;
use App\Models\Schedule;
use App\Models\SchoolSetting;
use App\Services\ScheduleGeneratorService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $generator;

    public function __construct(ScheduleGeneratorService $generator)
    {
        $this->generator = $generator;
    }

    public function index()
    {
        $totalTeachers = Teacher::count();
        $totalSubjects = Subject::count();
        $totalClassrooms = Classroom::count();
        $totalAssignments = TeacherAssignment::count();

        // Total hours needed vs scheduled
        $totalHoursTarget = TeacherAssignment::sum('hours_per_week');
        $totalHoursScheduled = Schedule::count();
        $scheduleProgress = $totalHoursTarget > 0 ? round(($totalHoursScheduled / $totalHoursTarget) * 100, 1) : 0;

        // Conflicts
        $conflicts = $this->generator->detectConflicts();

        // Teachers breakdown by load
        $teachers = Teacher::with('assignments')->get();
        $heavyLoadCount = 0; // > 30 jam
        $normalLoadCount = 0; // 24 - 30 jam
        $lightLoadCount = 0; // < 24 jam

        foreach ($teachers as $t) {
            $hours = $t->total_teaching_hours;
            if ($hours > 30) $heavyLoadCount++;
            elseif ($hours >= 24) $normalLoadCount++;
            else $lightLoadCount++;
        }

        // Subjects by category
        $subjectsByCategory = Subject::selectRaw('category, count(*) as count')
            ->groupBy('category')
            ->pluck('count', 'category')
            ->toArray();

        // Top 8 teachers with highest teaching hours
        $topTeachers = Teacher::withCount(['assignments as total_hours' => function ($q) {
            $q->select(\DB::raw('coalesce(sum(hours_per_week), 0)'));
        }])
        ->orderByDesc('total_hours')
        ->limit(8)
        ->get();

        // Classrooms schedule fullness
        $classrooms = Classroom::withCount([
            'schedules',
            'assignments as total_target_hours' => function ($q) {
                $q->select(\DB::raw('coalesce(sum(hours_per_week), 0)'));
            }
        ])->get();

        // Settings
        $schoolName = SchoolSetting::get('school_name', 'MADRASAH ALIYAH NEGERI 20 JAKARTA TIMUR');
        $academicYear = SchoolSetting::get('academic_year', '2026/2027');
        $semester = SchoolSetting::get('semester', 'Ganjil');

        return view('dashboard.index', compact(
            'totalTeachers',
            'totalSubjects',
            'totalClassrooms',
            'totalAssignments',
            'totalHoursTarget',
            'totalHoursScheduled',
            'scheduleProgress',
            'conflicts',
            'heavyLoadCount',
            'normalLoadCount',
            'lightLoadCount',
            'subjectsByCategory',
            'topTeachers',
            'classrooms',
            'schoolName',
            'academicYear',
            'semester'
        ));
    }
}
