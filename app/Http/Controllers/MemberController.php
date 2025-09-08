<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Carbon\Carbon;

class MemberController extends Controller
{
    public function index(Request $request): View
    {
        $query = Member::query();
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('member_id', 'like', "%{$search}%")
                  ->orWhere('kelas', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('kelas') && $request->kelas !== 'all') {
            $query->where('kelas', $request->kelas);
        }
        
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        
        $members = $query->get();
        $kelasList = ['7A', '7B', '7C', '8A', '8B', '8C', '9A', '9B', '9C'];
        
        return view('members.index', compact('members', 'kelasList'));
    }

    public function create(): View
    {
        return view('members.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'member_id' => 'required|string|max:50|unique:members',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'birth_date' => 'nullable|date',
            'registration_date' => 'required|date',
            'status' => 'required|in:active,inactive',
            'kelas' => 'required|in:7A,7B,7C,8A,8B,8C,9A,9B,9C',
        ]);

        Member::create($validated);

        return redirect()->route('members.index')
            ->with('success', 'Anggota berhasil ditambahkan!');
    }

    public function show(Member $member): View
    {
        $member->load('borrowings.book');
        return view('members.show', compact('member'));
    }

    public function edit(Member $member): View
    {
        return view('members.edit', compact('member'));
    }

    public function update(Request $request, Member $member): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'member_id' => 'required|string|max:50|unique:members,member_id,' . $member->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'birth_date' => 'nullable|date',
            'registration_date' => 'required|date',
            'status' => 'required|in:active,inactive',
            'kelas' => 'required|in:7A,7B,7C,8A,8B,8C,9A,9B,9C',
        ]);

        $member->update($validated);

        return redirect()->route('members.index')
            ->with('success', 'Anggota berhasil diupdate!');
    }

    public function destroy(Member $member): RedirectResponse
    {
        $member->delete();

        return redirect()->route('members.index')
            ->with('success', 'Anggota berhasil dihapus!');
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
                $nisn = $row[1]; // NISN di kolom B
                $nama = $row[2]; // Nama di kolom C
                $kelas = $row[3]; // Kelas di kolom D
                
                // Skip jika data kosong
                if (empty($nisn) || empty($nama)) {
                    continue;
                }
                
                // Cek apakah sudah ada
                $existingMember = Member::where('member_id', $nisn)->first();
                
                if (!$existingMember) {
                    Member::create([
                        'member_id' => $nisn,
                        'name' => $nama,
                        'kelas' => $kelas,
                        'phone' => null,
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



    public function checkNisn(Request $request)
    {
        $nisn = $request->input('nisn');
        $member = Member::where('member_id', $nisn)->first();
        
        if ($member) {
            return response()->json([
                'success' => true,
                'member' => $member
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Data siswa tidak ditemukan'
        ]);
    }
}
