<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\TeacherAssignment;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    public function index(Request $request)
    {
        $selectedTeacherId = $request->get('teacher_id');
        $selectedClassId = $request->get('classroom_id');

        $teachers = Teacher::orderBy('order_no')->get();
        $classrooms = Classroom::orderBy('grade')->orderBy('name')->get();
        $subjects = Subject::orderBy('name')->get();

        $query = TeacherAssignment::with('teacher', 'subject', 'classroom');

        if ($selectedTeacherId) {
            $query->where('teacher_id', $selectedTeacherId);
        }
        if ($selectedClassId) {
            $query->where('classroom_id', $selectedClassId);
        }

        $assignments = $query->paginate(20)->withQueryString();

        // Calculate distribution matrix for visual display
        $matrix = [];
        $allAssignments = TeacherAssignment::with('teacher', 'subject', 'classroom')->get();
        foreach ($allAssignments as $a) {
            $matrix[$a->teacher_id][$a->classroom_id][] = [
                'id' => $a->id,
                'subject' => $a->subject->name,
                'hours' => $a->hours_per_week,
                'color' => $a->subject->color,
            ];
        }

        return view('assignments.index', compact(
            'assignments', 'teachers', 'classrooms', 'subjects',
            'selectedTeacherId', 'selectedClassId', 'matrix'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
            'subject_id' => 'required|exists:subjects,id',
            'classroom_id' => 'required|exists:classrooms,id',
            'hours_per_week' => 'required|integer|min:1|max:10',
        ]);

        TeacherAssignment::updateOrCreate(
            [
                'teacher_id' => $validated['teacher_id'],
                'subject_id' => $validated['subject_id'],
                'classroom_id' => $validated['classroom_id'],
            ],
            [
                'hours_per_week' => $validated['hours_per_week'],
            ]
        );

        return back()->with('success', 'Distribusi tugas mengajar berhasil disimpan.');
    }

    public function destroy($id)
    {
        $assignment = TeacherAssignment::findOrFail($id);
        $assignment->delete();

        return back()->with('success', 'Distribusi tugas mengajar berhasil dihapus.');
    }
}
