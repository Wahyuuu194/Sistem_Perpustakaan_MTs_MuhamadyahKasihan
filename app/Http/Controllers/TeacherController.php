<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class TeacherController extends Controller
{
    public function index(): View
    {
        $teachers = Teacher::orderBy('name')->paginate(15);
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

    public function importFromCsv(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt'
        ]);
        
        $file = $request->file('csv_file');
        $csvData = array_map('str_getcsv', file($file->getPathname()));
        
        // Skip header row
        $csvData = array_slice($csvData, 1);
        
        $imported = 0;
        $skipped = 0;
        $errors = [];
        
        foreach ($csvData as $index => $row) {
            try {
                $nip = $row[0]; // NIP di kolom A
                $nama = $row[1]; // Nama di kolom B
                $subject = $row[2] ?? null; // Mata pelajaran di kolom C (opsional)
                $phone = $row[3] ?? null; // Telepon di kolom D (opsional)
                
                // Skip jika data kosong
                if (empty($nip) || empty($nama)) {
                    continue;
                }
                
                // Cek apakah sudah ada
                $existingTeacher = Teacher::where('teacher_id', $nip)->first();
                
                if (!$existingTeacher) {
                    Teacher::create([
                        'teacher_id' => $nip,
                        'name' => $nama,
                        'subject' => $subject,
                        'phone' => $phone,
                        'address' => null,
                        'birth_date' => null,
                        'registration_date' => now(),
                        'status' => 'active',
                    ]);
                    $imported++;
                } else {
                    $skipped++;
                }
            } catch (\Exception $e) {
                $errors[] = "Baris " . ($index + 2) . ": " . $e->getMessage();
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => "Berhasil import {$imported} data, {$skipped} data sudah ada",
            'imported' => $imported,
            'skipped' => $skipped,
            'errors' => $errors
        ]);
    }
}
