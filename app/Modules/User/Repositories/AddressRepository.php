<?php

namespace App\Modules\User\Repositories;

use App\Modules\User\Models\Address;
use App\Modules\User\Models\User;
use Illuminate\Support\Collection;

class AddressRepository
{
    /**
     * Get all addresses for a user
     */
    public function getByUser(User $user): Collection
    {
        return Address::where('user_id', $user->id)
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Find address by ID
     */
    public function findById(int $id): ?Address
    {
        return Address::find($id);
    }

    /**
     * Create address
     */
    public function create(array $data): Address
    {
        return Address::create($data);
    }

    /**
     * Update address
     */
    public function update(Address $address, array $data): Address
    {
        $address->update($data);
        return $address->fresh();
    }

    /**
     * Delete address
     */
    public function delete(Address $address): void
    {
        $address->delete();
    }

    /**
     * Clear default flag for all user addresses
     */
    public function clearDefaultForUser(User $user): void
    {
        Address::where('user_id', $user->id)
            ->where('is_default', true)
            ->update(['is_default' => false]);
    }
}
