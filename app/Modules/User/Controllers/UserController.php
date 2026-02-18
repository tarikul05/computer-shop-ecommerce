<?php

namespace App\Modules\User\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\User\Services\UserService;
use App\Modules\User\Requests\UpdateProfileRequest;
use App\Modules\User\Requests\UpdatePasswordRequest;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly UserService $userService
    ) {}

    /**
     * Get authenticated user's profile
     */
    public function profile(Request $request): JsonResponse
    {
        $user = $this->userService->getProfile($request->user());

        return $this->successResponse($user);
    }

    /**
     * Update user's profile
     */
    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        $user = $this->userService->updateProfile(
            $request->user(),
            $request->validated()
        );

        return $this->successResponse($user, 'Profile updated successfully');
    }

    /**
     * Update user's password
     */
    public function updatePassword(UpdatePasswordRequest $request): JsonResponse
    {
        $result = $this->userService->updatePassword(
            $request->user(),
            $request->validated()
        );

        if (!$result) {
            return $this->errorResponse('Current password is incorrect', 400);
        }

        return $this->successResponse(null, 'Password updated successfully');
    }

    /**
     * Delete user's account
     */
    public function deleteAccount(Request $request): JsonResponse
    {
        $this->userService->deleteAccount($request->user());

        return $this->successResponse(null, 'Account deleted successfully');
    }
}
