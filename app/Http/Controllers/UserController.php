<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\School;
use App\Models\Classroom;
use App\Models\Subject;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
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

        $users = User::with(['roles', 'school', 'homeroomClass', 'subjects'])
            ->when($selectedSchoolId, function ($query) use ($selectedSchoolId) {
                return $query->where('school_id', $selectedSchoolId);
            })
            ->orderBy('name')
            ->paginate(15);

        return view('admin.users.index', compact('users', 'schools', 'selectedSchoolId'));
    }

    public function create(Request $request)
    {
        $schoolId = Auth::user()->hasRole('Super Admin') ? ($request->query('school_id') ?? School::first()->id) : Auth::user()->school_id;
        
        $roles = Role::whereNotIn('name', ['Super Admin'])->get();
        $classrooms = Classroom::where('school_id', $schoolId)->orderBy('name')->get();
        $subjects = Subject::where('school_id', $schoolId)->orderBy('name')->get();
        
        return view('admin.users.create', compact('roles', 'schoolId', 'classrooms', 'subjects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'required|exists:roles,name',
            'school_id' => 'required|exists:schools,id',
            'classroom_id' => 'nullable|exists:classrooms,id',
            'subject_ids' => 'nullable|array'
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'school_id' => $request->school_id,
            ]);

            $user->assignRole($request->role);

            if ($request->role == 'Guru') {
                if ($request->filled('classroom_id')) {
                    Classroom::where('id', $request->classroom_id)->update(['teacher_id' => $user->id]);
                }
                if ($request->filled('subject_ids')) {
                    $user->subjects()->sync($request->subject_ids);
                }
            }

            DB::commit();
            return redirect()->route('users.index', ['school_id' => $request->school_id])->with('success', 'Pengguna berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $user = User::with(['subjects', 'homeroomClass'])->findOrFail($id);
        
        // PROTEKSI KEAMANAN ADMIN SEKOLAH
        if (!Auth::user()->hasRole('Super Admin') && $user->school_id !== Auth::user()->school_id) {
            abort(403, 'Akses ditolak. Anda tidak berhak mengedit pengguna dari sekolah lain.');
        }

        $schoolId = $user->school_id;
        $roles = Role::whereNotIn('name', ['Super Admin'])->get();
        $classrooms = Classroom::where('school_id', $schoolId)->orderBy('name')->get();
        $subjects = Subject::where('school_id', $schoolId)->orderBy('name')->get();
        
        return view('admin.users.edit', compact('user', 'roles', 'classrooms', 'subjects', 'schoolId'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        // PROTEKSI KEAMANAN ADMIN SEKOLAH
        if (!Auth::user()->hasRole('Super Admin') && $user->school_id !== Auth::user()->school_id) {
            abort(403, 'Akses ditolak. Anda tidak berhak mengedit pengguna dari sekolah lain.');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required|exists:roles,name',
            'classroom_id' => 'nullable|exists:classrooms,id',
            'subject_ids' => 'nullable|array'
        ]);

        DB::beginTransaction();
        try {
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);

            if ($request->filled('password')) {
                $user->update(['password' => Hash::make($request->password)]);
            }

            $user->syncRoles([$request->role]);

            if ($request->role == 'Guru') {
                if ($request->filled('classroom_id')) {
                    Classroom::where('teacher_id', $user->id)->update(['teacher_id' => null]);
                    Classroom::where('id', $request->classroom_id)->update(['teacher_id' => $user->id]);
                } else {
                    Classroom::where('teacher_id', $user->id)->update(['teacher_id' => null]);
                }
                $user->subjects()->sync($request->subject_ids ?? []);
            } else {
                Classroom::where('teacher_id', $user->id)->update(['teacher_id' => null]);
                $user->subjects()->detach();
            }

            DB::commit();
            return redirect()->route('users.index', ['school_id' => $user->school_id])->with('success', 'Data pengguna berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // PROTEKSI KEAMANAN ADMIN SEKOLAH
        if (!Auth::user()->hasRole('Super Admin') && $user->school_id !== Auth::user()->school_id) {
            abort(403, 'Akses ditolak. Anda tidak berhak menghapus pengguna dari sekolah lain.');
        }
        
        if (Auth::id() === $user->id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();
        return back()->with('success', 'Data pengguna berhasil dihapus.');
    }
}