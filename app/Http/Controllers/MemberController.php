<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Services\GoogleSheetsSyncService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Carbon\Carbon;

class MemberController extends Controller
{
    public function showImportForm(): View
    {
        return view('members.import');
    }

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





    public function checkNisn(Request $request)
    {
        $nisn = $request->input('nisn');
        
        // Cari member dengan NISN yang diberikan
        $member = Member::where('member_id', $nisn)->first();
        
        // Jika tidak ditemukan, coba dengan menghilangkan leading zero
        if (!$member && str_starts_with($nisn, '0')) {
            $nisnWithoutZero = ltrim($nisn, '0');
            $member = Member::where('member_id', $nisnWithoutZero)->first();
        }
        
        // Jika masih tidak ditemukan, coba dengan menambahkan leading zero
        if (!$member && !str_starts_with($nisn, '0')) {
            $nisnWithZero = '0' . $nisn;
            $member = Member::where('member_id', $nisnWithZero)->first();
        }
        
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

    public function syncFromGoogleSheets(Request $request)
    {
        try {
            $syncService = new GoogleSheetsSyncService();
            $result = $syncService->syncStudents();
            
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
