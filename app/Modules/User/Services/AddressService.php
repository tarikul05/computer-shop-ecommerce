<?php

namespace App\Modules\User\Services;

use App\Modules\User\Models\Address;
use App\Modules\User\Models\User;
use App\Modules\User\Repositories\AddressRepository;
use Illuminate\Support\Collection;

class AddressService
{
    public function __construct(
        private readonly AddressRepository $addressRepository
    ) {}

    /**
     * Get all addresses for a user
     */
    public function getUserAddresses(User $user): Collection
    {
        return $this->addressRepository->getByUser($user);
    }

    /**
     * Create a new address
     */
    public function createAddress(User $user, array $data): Address
    {
        $data['user_id'] = $user->id;

        // If this is the first address or is_default is true, make it default
        $existingCount = $this->addressRepository->getByUser($user)->count();
        
        if ($existingCount === 0 || ($data['is_default'] ?? false)) {
            $this->addressRepository->clearDefaultForUser($user);
            $data['is_default'] = true;
        }

        return $this->addressRepository->create($data);
    }

    /**
     * Update an address
     */
    public function updateAddress(Address $address, array $data): Address
    {
        if ($data['is_default'] ?? false) {
            $this->addressRepository->clearDefaultForUser($address->user);
        }

        return $this->addressRepository->update($address, $data);
    }

    /**
     * Delete an address
     */
    public function deleteAddress(Address $address): void
    {
        $wasDefault = $address->is_default;
        $user = $address->user;

        $this->addressRepository->delete($address);

        // If deleted address was default, make another one default
        if ($wasDefault) {
            $firstAddress = $this->addressRepository->getByUser($user)->first();
            if ($firstAddress) {
                $this->addressRepository->update($firstAddress, ['is_default' => true]);
            }
        }
    }

    /**
     * Set an address as default
     */
    public function setDefaultAddress(User $user, Address $address): Address
    {
        $this->addressRepository->clearDefaultForUser($user);

        return $this->addressRepository->update($address, ['is_default' => true]);
    }
}
