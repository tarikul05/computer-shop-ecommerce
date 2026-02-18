<?php

namespace App\Modules\Order\Repositories;

use App\Modules\Order\Models\Order;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class OrderRepository
{
    /**
     * Get all orders (paginated) with filters
     */
    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Order::with(['user', 'items']);

        // Filter by status
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Filter by payment status
        if (!empty($filters['payment_status'])) {
            $query->where('payment_status', $filters['payment_status']);
        }

        // Filter by user
        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        // Filter by date range
        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        // Search by order number
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('order_number', 'like', "%{$filters['search']}%")
                  ->orWhereHas('user', function ($q2) use ($filters) {
                      $q2->where('name', 'like', "%{$filters['search']}%")
                         ->orWhere('email', 'like', "%{$filters['search']}%");
                  });
            });
        }

        // Sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortDir = $filters['sort_dir'] ?? 'desc';
        $query->orderBy($sortBy, $sortDir);

        return $query->paginate($perPage);
    }

    /**
     * Get orders by user
     */
    public function getByUser(User $user, int $perPage = 10): LengthAwarePaginator
    {
        return Order::with(['items.product'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get order by ID
     */
    public function getById(int $id): ?Order
    {
        return Order::with(['user', 'items.product', 'coupon', 'statusHistory.changedBy'])
            ->find($id);
    }

    /**
     * Get order by order number
     */
    public function getByOrderNumber(string $orderNumber): ?Order
    {
        return Order::with(['user', 'items.product', 'coupon', 'statusHistory.changedBy'])
            ->where('order_number', $orderNumber)
            ->first();
    }

    /**
     * Get order by ID for a specific user
     */
    public function getByIdForUser(int $id, User $user): ?Order
    {
        return Order::with(['items.product', 'statusHistory'])
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->first();
    }

    /**
     * Create order
     */
    public function create(array $data): Order
    {
        return Order::create($data);
    }

    /**
     * Update order
     */
    public function update(Order $order, array $data): Order
    {
        $order->update($data);
        return $order->fresh();
    }

    /**
     * Add order item
     */
    public function addItem(Order $order, array $itemData): void
    {
        $order->items()->create($itemData);
    }

    /**
     * Add status history
     */
    public function addStatusHistory(Order $order, string $status, ?string $comment = null, ?int $changedBy = null): void
    {
        $order->statusHistory()->create([
            'status' => $status,
            'comment' => $comment,
            'changed_by' => $changedBy,
        ]);
    }

    /**
     * Get order statistics
     */
    public function getStatistics(array $filters = []): array
    {
        $query = Order::query();

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        $totalOrders = (clone $query)->count();
        $totalRevenue = (clone $query)->where('payment_status', 'paid')->sum('total');
        $pendingOrders = (clone $query)->where('status', 'pending')->count();
        $processingOrders = (clone $query)->where('status', 'processing')->count();
        $deliveredOrders = (clone $query)->where('status', 'delivered')->count();
        $cancelledOrders = (clone $query)->where('status', 'cancelled')->count();

        return [
            'total_orders' => $totalOrders,
            'total_revenue' => $totalRevenue,
            'pending_orders' => $pendingOrders,
            'processing_orders' => $processingOrders,
            'delivered_orders' => $deliveredOrders,
            'cancelled_orders' => $cancelledOrders,
        ];
    }

    /**
     * Get recent orders
     */
    public function getRecent(int $limit = 10): Collection
    {
        return Order::with(['user', 'items'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
