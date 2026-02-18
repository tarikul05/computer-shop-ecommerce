<?php

namespace App\Modules\Auth\Services;

use App\Modules\Auth\Repositories\AuthRepository;
use App\Modules\User\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AuthService
{
    public function __construct(
        private readonly AuthRepository $authRepository
    ) {}

    /**
     * Register a new user
     */
    public function register(array $data): array
    {
        $user = $this->authRepository->createUser([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $this->formatUserData($user),
            'token' => $token,
            'token_type' => 'Bearer',
        ];
    }

    /**
     * Login user
     */
    public function login(array $credentials): ?array
    {
        $user = $this->authRepository->findByEmail($credentials['email']);

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return null;
        }

        // Revoke existing tokens if needed
        if (isset($credentials['revoke_existing']) && $credentials['revoke_existing']) {
            $user->tokens()->delete();
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $this->formatUserData($user),
            'token' => $token,
            'token_type' => 'Bearer',
        ];
    }

    /**
     * Logout user
     */
    public function logout(User $user): void
    {
        // Revoke current token
        $user->currentAccessToken()->delete();
    }

    /**
     * Get authenticated user data
     */
    public function getAuthenticatedUser(User $user): array
    {
        return $this->formatUserData($user->fresh());
    }

    /**
     * Send password reset link
     */
    public function sendPasswordResetLink(array $data): bool
    {
        $status = Password::sendResetLink(['email' => $data['email']]);

        return $status === Password::RESET_LINK_SENT;
    }

    /**
     * Reset password
     */
    public function resetPassword(array $data): bool
    {
        $status = Password::reset(
            [
                'email' => $data['email'],
                'password' => $data['password'],
                'password_confirmation' => $data['password_confirmation'],
                'token' => $data['token'],
            ],
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                // Revoke all tokens
                $user->tokens()->delete();
            }
        );

        return $status === Password::PASSWORD_RESET;
    }

    /**
     * Refresh token
     */
    public function refreshToken(User $user): array
    {
        // Delete current token
        $user->currentAccessToken()->delete();

        // Create new token
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'token' => $token,
            'token_type' => 'Bearer',
        ];
    }

    /**
     * Format user data for response
     */
    private function formatUserData(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'email_verified_at' => $user->email_verified_at,
            'created_at' => $user->created_at,
        ];
    }
}
