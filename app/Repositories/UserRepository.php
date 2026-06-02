<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    public function getAllPaginated(int $perPage = 10)
    {
        return User::with(['roles', 'school'])->latest()->paginate($perPage);
    }

    public function findById(string $id)
    {
        return User::findOrFail($id);
    }

    public function create(array $data)
    {
        return User::create($data);
    }

    public function update(string $id, array $data)
    {
        $user = $this->findById($id);
        $user->update($data);
        return $user;
    }

    public function delete(string $id)
    {
        $user = $this->findById($id);
        return $user->delete();
    }
}