<?php

namespace App\Modules\Order\Services;

use App\Modules\Order\Models\Order;
use App\Modules\Order\Repositories\OrderRepository;
use App\Modules\Cart\Services\CartService;
use App\Modules\Coupon\Services\CouponService;
use App\Modules\User\Repositories\AddressRepository;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(
        private readonly OrderRepository $orderRepository,
        private readonly CartService $cartService,
        private readonly CouponService $couponService,
        private readonly AddressRepository $addressRepository
    ) {}

    /**
     * Get paginated orders (admin)
     */
    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->orderRepository->getPaginated($filters, $perPage);
    }

    /**
     * Get user orders
     */
    public function getUserOrders(User $user, int $perPage = 10): LengthAwarePaginator
    {
        return $this->orderRepository->getByUser($user, $perPage);
    }

    /**
     * Get order by ID
     */
    public function getById(int $id): ?Order
    {
        return $this->orderRepository->getById($id);
    }

    /**
     * Get order by order number
     */
    public function getByOrderNumber(string $orderNumber): ?Order
    {
        return $this->orderRepository->getByOrderNumber($orderNumber);
    }

    /**
     * Get user's order by ID
     */
    public function getUserOrder(User $user, int $orderId): ?Order
    {
        return $this->orderRepository->getByIdForUser($orderId, $user);
    }

    /**
     * Create order from cart (checkout)
     */
    public function createFromCart(User $user, array $data): array
    {
        // Get cart
        $cart = $this->cartService->getCart($user, null);
        
        if (empty($cart['items'])) {
            throw new \Exception('Cart is empty');
        }

        // Validate addresses
        $shippingAddress = $this->addressRepository->getById($data['shipping_address_id']);
        if (!$shippingAddress || $shippingAddress->user_id !== $user->id) {
            throw new \Exception('Invalid shipping address');
        }

        $billingAddressId = $data['billing_address_id'] ?? $data['shipping_address_id'];
        $billingAddress = $this->addressRepository->getById($billingAddressId);
        if (!$billingAddress || $billingAddress->user_id !== $user->id) {
            throw new \Exception('Invalid billing address');
        }

        // Calculate amounts
        $subtotal = $cart['subtotal'];
        $discountAmount = $cart['discount'] ?? 0;
        $shippingAmount = $this->calculateShipping($data['shipping_method'] ?? 'standard', $subtotal);
        $taxAmount = 0; // Can be calculated based on region
        $total = $subtotal - $discountAmount + $shippingAmount + $taxAmount;

        return DB::transaction(function () use ($user, $data, $cart, $shippingAddress, $billingAddress, $subtotal, $discountAmount, $shippingAmount, $taxAmount, $total) {
            // Create order
            $order = $this->orderRepository->create([
                'user_id' => $user->id,
                'shipping_address_id' => $shippingAddress->id,
                'billing_address_id' => $billingAddress->id,
                'status' => Order::STATUS_PENDING,
                'payment_status' => Order::PAYMENT_PENDING,
                'payment_method' => $data['payment_method'] ?? 'cod',
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'shipping_amount' => $shippingAmount,
                'tax_amount' => $taxAmount,
                'total' => $total,
                'coupon_id' => $cart['coupon_id'] ?? null,
                'coupon_code' => $cart['coupon_code'] ?? null,
                'shipping_method' => $data['shipping_method'] ?? 'standard',
                'customer_notes' => $data['customer_notes'] ?? null,
                'shipping_address_snapshot' => $shippingAddress->toArray(),
                'billing_address_snapshot' => $billingAddress->toArray(),
            ]);

            // Add order items
            foreach ($cart['items'] as $item) {
                $this->orderRepository->addItem($order, [
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'product_sku' => $item['product_sku'] ?? null,
                    'product_image' => $item['product_image'] ?? null,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['total_price'],
                ]);
            }

            // Add initial status history
            $this->orderRepository->addStatusHistory($order, Order::STATUS_PENDING, 'Order placed');

            // Increment coupon usage
            if (!empty($cart['coupon_id'])) {
                $this->couponService->incrementUsage($cart['coupon_id'], $user->id);
            }

            // Clear cart
            $this->cartService->clearCart($user, null);

            return [
                'order' => $order->load(['items', 'statusHistory']),
                'message' => 'Order placed successfully',
            ];
        });
    }

    /**
     * Update order status
     */
    public function updateStatus(Order $order, string $status, ?string $comment = null, ?int $changedBy = null): Order
    {
        $oldStatus = $order->status;

        $order = $this->orderRepository->update($order, ['status' => $status]);

        // Update timestamps
        if ($status === Order::STATUS_SHIPPED) {
            $this->orderRepository->update($order, ['shipped_at' => now()]);
        } elseif ($status === Order::STATUS_DELIVERED) {
            $this->orderRepository->update($order, ['delivered_at' => now()]);
        }

        // Add status history
        $this->orderRepository->addStatusHistory(
            $order,
            $status,
            $comment ?? "Status changed from {$oldStatus} to {$status}",
            $changedBy
        );

        return $order->fresh(['items', 'statusHistory']);
    }

    /**
     * Update payment status
     */
    public function updatePaymentStatus(Order $order, string $paymentStatus, ?string $transactionId = null): Order
    {
        $data = ['payment_status' => $paymentStatus];
        
        if ($transactionId) {
            $data['transaction_id'] = $transactionId;
        }

        // If paid, confirm order
        if ($paymentStatus === Order::PAYMENT_PAID && $order->status === Order::STATUS_PENDING) {
            $data['status'] = Order::STATUS_CONFIRMED;
            $this->orderRepository->addStatusHistory($order, Order::STATUS_CONFIRMED, 'Payment received');
        }

        return $this->orderRepository->update($order, $data);
    }

    /**
     * Cancel order
     */
    public function cancelOrder(Order $order, ?string $reason = null, ?int $cancelledBy = null): Order
    {
        if (!$order->canBeCancelled()) {
            throw new \Exception('Order cannot be cancelled at this stage');
        }

        $order = $this->orderRepository->update($order, [
            'status' => Order::STATUS_CANCELLED,
        ]);

        $this->orderRepository->addStatusHistory(
            $order,
            Order::STATUS_CANCELLED,
            $reason ?? 'Order cancelled',
            $cancelledBy
        );

        // TODO: Restore product stock
        // TODO: Refund if paid

        return $order->fresh(['items', 'statusHistory']);
    }

    /**
     * Update tracking info
     */
    public function updateTracking(Order $order, string $trackingNumber): Order
    {
        return $this->orderRepository->update($order, [
            'tracking_number' => $trackingNumber,
        ]);
    }

    /**
     * Get order statistics
     */
    public function getStatistics(array $filters = []): array
    {
        return $this->orderRepository->getStatistics($filters);
    }

    /**
     * Calculate shipping cost
     */
    protected function calculateShipping(string $method, float $subtotal): float
    {
        // Free shipping for orders over 5000
        if ($subtotal >= 5000) {
            return 0;
        }

        return match($method) {
            'express' => 150,
            'standard' => 80,
            'pickup' => 0,
            default => 80,
        };
    }

    /**
     * Format order for API response
     */
    public function formatOrder(Order $order): array
    {
        return [
            'id' => $order->id,
            'order_number' => $order->order_number,
            'status' => $order->status,
            'status_label' => $order->status_label,
            'payment_status' => $order->payment_status,
            'payment_status_label' => $order->payment_status_label,
            'payment_method' => $order->payment_method,
            'subtotal' => (float) $order->subtotal,
            'discount_amount' => (float) $order->discount_amount,
            'shipping_amount' => (float) $order->shipping_amount,
            'tax_amount' => (float) $order->tax_amount,
            'total' => (float) $order->total,
            'coupon_code' => $order->coupon_code,
            'shipping_method' => $order->shipping_method,
            'tracking_number' => $order->tracking_number,
            'customer_notes' => $order->customer_notes,
            'shipping_address' => $order->shipping_address_snapshot,
            'billing_address' => $order->billing_address_snapshot,
            'items' => $order->items->map(fn ($item) => [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'product_name' => $item->product_name,
                'product_sku' => $item->product_sku,
                'product_image' => $item->product_image,
                'quantity' => $item->quantity,
                'unit_price' => (float) $item->unit_price,
                'total_price' => (float) $item->total_price,
            ]),
            'status_history' => $order->statusHistory->map(fn ($history) => [
                'status' => $history->status,
                'comment' => $history->comment,
                'changed_by' => $history->changedBy?->name,
                'created_at' => $history->created_at->toISOString(),
            ]),
            'shipped_at' => $order->shipped_at?->toISOString(),
            'delivered_at' => $order->delivered_at?->toISOString(),
            'created_at' => $order->created_at->toISOString(),
            'updated_at' => $order->updated_at->toISOString(),
        ];
    }
}
