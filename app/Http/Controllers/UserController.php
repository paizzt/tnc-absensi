<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\UserService;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Spatie\Permission\Models\Role;
use App\Models\School;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index()
    {
        $users = $this->userService->getAllUsers();
        return view('superadmin.users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::all();
        $schools = School::orderBy('name', 'asc')->get(); 
        
        return view('superadmin.users.create', compact('roles', 'schools'));
    }

    public function store(StoreUserRequest $request)
    {
        $this->userService->createUserWithRole($request->validated());
        return redirect()->route('users.index')->with('success', 'Akun pengguna berhasil dibuat.');
    }

    public function edit($id)
    {
        $user = $this->userService->getUserById($id);
        $roles = Role::all();
        $schools = School::orderBy('name', 'asc')->get(); 
        
        return view('superadmin.users.edit', compact('user', 'roles', 'schools'));
    }

    public function update(UpdateUserRequest $request, $id)
    {
        $this->userService->updateUserWithRole($id, $request->validated());
        return redirect()->route('users.index')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $this->userService->deleteUser($id);
        return redirect()->route('users.index')->with('success', 'Pengguna berhasil dihapus dari sistem.');
    }
}