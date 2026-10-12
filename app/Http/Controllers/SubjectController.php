<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Subject::withCount('assignments');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $subjects = $query->orderBy('name')->paginate(15)->withQueryString();
        $categories = Subject::distinct()->pluck('category')->filter();

        return view('subjects.index', compact('subjects', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:subjects,code',
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'color' => 'required|string|max:20',
        ]);

        Subject::create($validated);

        return back()->with('success', 'Mata pelajaran berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $subject = Subject::findOrFail($id);

        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:subjects,code,' . $id,
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'color' => 'required|string|max:20',
        ]);

        $subject->update($validated);

        return back()->with('success', 'Mata pelajaran berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $subject = Subject::findOrFail($id);
        $subject->delete();

        return back()->with('success', 'Mata pelajaran berhasil dihapus.');
    }
}
