<?php

namespace App\Modules\User\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\User\Services\AddressService;
use App\Modules\User\Requests\StoreAddressRequest;
use App\Modules\User\Requests\UpdateAddressRequest;
use App\Modules\User\Models\Address;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly AddressService $addressService
    ) {}

    /**
     * Get all addresses for authenticated user
     */
    public function index(Request $request): JsonResponse
    {
        $addresses = $this->addressService->getUserAddresses($request->user());

        return $this->successResponse($addresses);
    }

    /**
     * Store a new address
     */
    public function store(StoreAddressRequest $request): JsonResponse
    {
        $address = $this->addressService->createAddress(
            $request->user(),
            $request->validated()
        );

        return $this->createdResponse($address, 'Address created successfully');
    }

    /**
     * Get a specific address
     */
    public function show(Request $request, Address $address): JsonResponse
    {
        // Check if address belongs to user
        if ($address->user_id !== $request->user()->id) {
            return $this->forbiddenResponse();
        }

        return $this->successResponse($address);
    }

    /**
     * Update an address
     */
    public function update(UpdateAddressRequest $request, Address $address): JsonResponse
    {
        // Check if address belongs to user
        if ($address->user_id !== $request->user()->id) {
            return $this->forbiddenResponse();
        }

        $address = $this->addressService->updateAddress($address, $request->validated());

        return $this->successResponse($address, 'Address updated successfully');
    }

    /**
     * Delete an address
     */
    public function destroy(Request $request, Address $address): JsonResponse
    {
        // Check if address belongs to user
        if ($address->user_id !== $request->user()->id) {
            return $this->forbiddenResponse();
        }

        $this->addressService->deleteAddress($address);

        return $this->successResponse(null, 'Address deleted successfully');
    }

    /**
     * Set address as default
     */
    public function setDefault(Request $request, Address $address): JsonResponse
    {
        // Check if address belongs to user
        if ($address->user_id !== $request->user()->id) {
            return $this->forbiddenResponse();
        }

        $address = $this->addressService->setDefaultAddress($request->user(), $address);

        return $this->successResponse($address, 'Default address updated');
    }
}
