<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Services\GoogleSheetsSyncService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class TeacherController extends Controller
{
    public function showImportForm(): View
    {
        return view('teachers.import');
    }

    public function index(Request $request): View
    {
        $query = Teacher::query();
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('teacher_id', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }
        
        $teachers = $query->orderBy('name')->paginate(15);
        return view('teachers.index', compact('teachers'));
    }

    public function create(): View
    {
        return view('teachers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'teacher_id' => 'required|string|max:50|unique:teachers',
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'birth_date' => 'nullable|date',
            'registration_date' => 'required|date',
            'status' => 'required|in:active,inactive',
            'subject' => 'nullable|string|max:255',
        ]);

        Teacher::create($validated);

        return redirect()->route('teachers.index')
            ->with('success', 'Data guru berhasil ditambahkan!');
    }

    public function show(Teacher $teacher): View
    {
        $teacher->load('borrowings.book');
        return view('teachers.show', compact('teacher'));
    }

    public function edit(Teacher $teacher): View
    {
        return view('teachers.edit', compact('teacher'));
    }

    public function update(Request $request, Teacher $teacher): RedirectResponse
    {
        $validated = $request->validate([
            'teacher_id' => 'required|string|max:50|unique:teachers,teacher_id,' . $teacher->id,
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'birth_date' => 'nullable|date',
            'registration_date' => 'required|date',
            'status' => 'required|in:active,inactive',
            'subject' => 'nullable|string|max:255',
        ]);

        $teacher->update($validated);

        return redirect()->route('teachers.index')
            ->with('success', 'Data guru berhasil diupdate!');
    }

    public function destroy(Teacher $teacher): RedirectResponse
    {
        $teacher->delete();

        return redirect()->route('teachers.index')
            ->with('success', 'Data guru berhasil dihapus!');
    }

    public function checkNip(Request $request)
    {
        $nip = $request->input('nip');
        $teacher = Teacher::where('teacher_id', $nip)->first();
        
        if ($teacher) {
            return response()->json([
                'success' => true,
                'teacher' => $teacher
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Data guru tidak ditemukan'
        ]);
    }


    public function syncFromGoogleSheets(Request $request)
    {
        try {
            $syncService = new GoogleSheetsSyncService();
            $result = $syncService->syncTeachers();
            
            $message = "Sync berhasil! ";
            $message .= "Imported: {$result['imported']}, ";
            $message .= "Updated: {$result['updated']}, ";
            $message .= "Total processed: {$result['total_processed']}";
            
            if (!empty($result['errors'])) {
                $message .= ". Errors: " . count($result['errors']);
            }
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $result
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sync gagal: ' . $e->getMessage()
            ], 500);
        }
    }

}
