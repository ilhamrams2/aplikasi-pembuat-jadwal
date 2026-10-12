<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Teacher;
use Illuminate\Http\Request;

class ClassroomController extends Controller
{
    public function index()
    {
        $classrooms = Classroom::with('homeroomTeacher')
            ->withCount(['assignments as total_hours' => function ($q) {
                $q->select(\DB::raw('coalesce(sum(hours_per_week), 0)'));
            }])
            ->orderBy('grade')
            ->orderBy('name')
            ->get();

        $teachers = Teacher::orderBy('name')->get();

        return view('classrooms.index', compact('classrooms', 'teachers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:classrooms,name',
            'grade' => 'required|integer|in:10,11,12',
            'homeroom_teacher_id' => 'nullable|exists:teachers,id',
        ]);

        Classroom::create($validated);

        return back()->with('success', 'Kelas / Rombel berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $classroom = Classroom::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:classrooms,name,' . $id,
            'grade' => 'required|integer|in:10,11,12',
            'homeroom_teacher_id' => 'nullable|exists:teachers,id',
        ]);

        $classroom->update($validated);

        return back()->with('success', 'Data kelas berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $classroom = Classroom::findOrFail($id);
        $classroom->delete();

        return back()->with('success', 'Data kelas berhasil dihapus.');
    }
}
