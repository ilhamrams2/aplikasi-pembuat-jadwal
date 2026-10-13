<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\Subject;
use App\Models\Classroom;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    public function index(Request $request)
    {
        $query = Teacher::with('assignments.subject', 'assignments.classroom');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhere('additional_role', 'like', "%{$search}%");
            });
        }

        if ($request->filled('load_filter')) {
            if ($request->load_filter === 'heavy') {
                $query->whereHas('assignments', function () {}, '>=', 1);
            }
        }

        $teachers = $query->orderBy('order_no')->paginate(15)->withQueryString();
        $allSubjects = Subject::orderBy('name')->get();
        $allClassrooms = Classroom::orderBy('name')->get();

        return view('teachers.index', compact('teachers', 'allSubjects', 'allClassrooms'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nip' => 'nullable|string|max:50',
            'additional_role' => 'nullable|string|max:255',
            'target_hours' => 'required|integer|min:0|max:60',
            'phone' => 'nullable|string|max:30',
        ]);

        $maxOrder = Teacher::max('order_no') ?? 0;
        $validated['order_no'] = $maxOrder + 1;
        $validated['color'] = '#' . substr(md5($validated['name']), 0, 6);

        if ($request->hasFile('signature_image')) {
            $request->validate(['signature_image' => 'image|mimes:jpeg,png,jpg,webp,svg|max:3072']);
            $file = $request->file('signature_image');
            $filename = 'teacher_' . time() . '_' . rand(100,999) . '.' . $file->getClientOriginalExtension();
            $destination = public_path('uploads/signatures');
            if (!\Illuminate\Support\Facades\File::isDirectory($destination)) {
                \Illuminate\Support\Facades\File::makeDirectory($destination, 0755, true, true);
            }
            $file->move($destination, $filename);
            $validated['signature_image'] = 'uploads/signatures/' . $filename;
        }

        Teacher::create($validated);

        return back()->with('success', 'Data guru berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $teacher = Teacher::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nip' => 'nullable|string|max:50',
            'additional_role' => 'nullable|string|max:255',
            'target_hours' => 'required|integer|min:0|max:60',
            'phone' => 'nullable|string|max:30',
        ]);

        if ($request->boolean('remove_signature_image')) {
            if ($teacher->signature_image && \Illuminate\Support\Facades\File::exists(public_path($teacher->signature_image))) {
                \Illuminate\Support\Facades\File::delete(public_path($teacher->signature_image));
            }
            $validated['signature_image'] = null;
        } elseif ($request->hasFile('signature_image')) {
            $request->validate(['signature_image' => 'image|mimes:jpeg,png,jpg,webp,svg|max:3072']);
            $file = $request->file('signature_image');
            $filename = 'teacher_' . $teacher->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $destination = public_path('uploads/signatures');
            if (!\Illuminate\Support\Facades\File::isDirectory($destination)) {
                \Illuminate\Support\Facades\File::makeDirectory($destination, 0755, true, true);
            }
            if ($teacher->signature_image && \Illuminate\Support\Facades\File::exists(public_path($teacher->signature_image))) {
                \Illuminate\Support\Facades\File::delete(public_path($teacher->signature_image));
            }
            $file->move($destination, $filename);
            $validated['signature_image'] = 'uploads/signatures/' . $filename;
        }

        $teacher->update($validated);

        return back()->with('success', 'Data guru berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $teacher = Teacher::findOrFail($id);
        $teacher->delete();

        return back()->with('success', 'Data guru berhasil dihapus.');
    }
}
