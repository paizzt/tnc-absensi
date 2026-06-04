<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Classroom;
use App\Models\School;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ClassroomController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $schools = [];
        $selectedSchoolId = null;

        if ($user->hasRole('Super Admin')) {
            $schools = School::orderBy('name')->get();
            $selectedSchoolId = $request->query('school_id') ?? ($schools->first()->id ?? null);
        } else {
            $selectedSchoolId = $user->school_id;
        }

        // Ambil data kelas beserta wali kelasnya
        $classrooms = Classroom::with('homeroomTeacher')
            ->where('school_id', $selectedSchoolId)
            ->orderBy('level')
            ->orderBy('name')
            ->get();

        // Ambil data guru untuk dimasukkan ke dalam dropdown pilihan wali kelas
        $teachers = User::role(['Wali Kelas', 'Guru Mata Pelajaran'])
            ->where('school_id', $selectedSchoolId)
            ->orderBy('name')
            ->get();

        return view('admin.classrooms.index', compact('classrooms', 'schools', 'selectedSchoolId', 'teachers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'school_id' => 'required|exists:schools,id',
            'level' => 'required|integer|min:1|max:12',
            'name' => 'required|string|max:50',
            'teacher_id' => 'nullable|exists:users,id'
        ]);

        Classroom::create($request->all());

        return back()->with('success', 'Data kelas berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'level' => 'required|integer|min:1|max:12',
            'name' => 'required|string|max:50',
            'teacher_id' => 'nullable|exists:users,id'
        ]);

        $classroom = Classroom::findOrFail($id);
        $classroom->update($request->only(['level', 'name', 'teacher_id']));

        return back()->with('success', 'Data kelas berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $classroom = Classroom::findOrFail($id);
        $classroom->delete();

        return back()->with('success', 'Data kelas berhasil dihapus.');
    }
}