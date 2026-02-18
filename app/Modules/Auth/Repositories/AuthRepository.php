<?php

namespace App\Modules\Auth\Repositories;

use App\Modules\User\Models\User;

class AuthRepository
{
    /**
     * Create a new user
     */
    public function createUser(array $data): User
    {
        return User::create($data);
    }

    /**
     * Find user by email
     */
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    /**
     * Find user by ID
     */
    public function findById(int $id): ?User
    {
        return User::find($id);
    }

    /**
     * Update user
     */
    public function updateUser(User $user, array $data): User
    {
        $user->update($data);
        return $user->fresh();
    }
}
