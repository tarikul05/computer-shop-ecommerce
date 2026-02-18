<?php

namespace App\Modules\User\Services;

use App\Modules\User\Models\User;
use App\Modules\User\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function __construct(
        private readonly UserRepository $userRepository
    ) {}

    /**
     * Get user profile
     */
    public function getProfile(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'avatar' => $user->avatar,
            'email_verified_at' => $user->email_verified_at,
            'created_at' => $user->created_at,
            'default_address' => $user->default_address,
        ];
    }

    /**
     * Update user profile
     */
    public function updateProfile(User $user, array $data): array
    {
        $user = $this->userRepository->update($user, $data);

        return $this->getProfile($user);
    }

    /**
     * Update user password
     */
    public function updatePassword(User $user, array $data): bool
    {
        // Verify current password
        if (!Hash::check($data['current_password'], $user->password)) {
            return false;
        }

        $this->userRepository->update($user, [
            'password' => Hash::make($data['password']),
        ]);

        // Revoke all tokens except current
        $user->tokens()->where('id', '!=', $user->currentAccessToken()->id)->delete();

        return true;
    }

    /**
     * Delete user account
     */
    public function deleteAccount(User $user): void
    {
        // Revoke all tokens
        $user->tokens()->delete();

        // Delete user
        $this->userRepository->delete($user);
    }
}
