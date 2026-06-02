<?php

namespace App\Services;

use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Exception;

class UserService
{
    protected $userRepo;

    public function __construct(UserRepositoryInterface $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    public function getAllUsers()
    {
        return $this->userRepo->getAllPaginated(10);
    }

    public function getUserById(string $id)
    {
        return $this->userRepo->findById($id);
    }

    public function createUserWithRole(array $data)
    {
        DB::beginTransaction();
        try {
            $data['password'] = Hash::make($data['password']);
            
            if ($data['role'] === 'Super Admin') {
                $data['school_id'] = null;
            }

            $user = $this->userRepo->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'school_id' => $data['school_id'],
                'is_active' => true,
            ]);

            $user->assignRole($data['role']);

            DB::commit();
            return $user;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateUserWithRole(string $id, array $data)
    {
        DB::beginTransaction();
        try {
            $user = $this->userRepo->findById($id);

            // Jika role Super Admin, kosongkan school_id
            if ($data['role'] === 'Super Admin') {
                $data['school_id'] = null;
            }

            // Persiapkan data yang akan diupdate
            $updateData = [
                'name' => $data['name'],
                'email' => $data['email'],
                'school_id' => $data['school_id'],
                'is_active' => $data['is_active'],
            ];

            // Hanya update password jika form password diisi
            if (!empty($data['password'])) {
                $updateData['password'] = Hash::make($data['password']);
            }

            $this->userRepo->update($id, $updateData);

            // Sinkronisasi Role (Hapus role lama, ganti yang baru)
            $user->syncRoles([$data['role']]);

            DB::commit();
            return $user;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function deleteUser(string $id)
    {
        return $this->userRepo->delete($id);
    }
}