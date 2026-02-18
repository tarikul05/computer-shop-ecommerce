<?php

namespace Database\Seeders;

use App\Models\User;
use App\Modules\Order\Models\Order;
use App\Modules\Order\Models\OrderItem;
use App\Modules\Order\Models\OrderStatusHistory;
use App\Modules\Product\Models\Product;
use App\Modules\User\Models\Address;
use App\Modules\Coupon\Models\Coupon;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $customers = User::where('role', 'customer')->get();
        $products = Product::all();
        $coupons = Coupon::where('is_active', true)->get();

        if ($customers->isEmpty() || $products->isEmpty()) {
            $this->command->warn('Please run UserSeeder and ProductSeeder first');
            return;
        }

        $statuses = [
            Order::STATUS_PENDING,
            Order::STATUS_CONFIRMED,
            Order::STATUS_PROCESSING,
            Order::STATUS_SHIPPED,
            Order::STATUS_DELIVERED,
            Order::STATUS_CANCELLED,
        ];

        $paymentMethods = ['cod', 'bkash', 'nagad', 'card'];
        $shippingMethods = ['standard', 'express'];

        $orderCount = 0;

        foreach ($customers as $customer) {
            // Each customer gets 2-5 orders
            $numOrders = rand(2, 5);

            for ($i = 0; $i < $numOrders; $i++) {
                $address = Address::where('user_id', $customer->id)->first();
                
                if (!$address) {
                    continue;
                }

                $status = $statuses[array_rand($statuses)];
                $paymentMethod = $paymentMethods[array_rand($paymentMethods)];
                $shippingMethod = $shippingMethods[array_rand($shippingMethods)];

                // Determine payment status based on order status
                $paymentStatus = Order::PAYMENT_PENDING;
                if (in_array($status, [Order::STATUS_CONFIRMED, Order::STATUS_PROCESSING, Order::STATUS_SHIPPED, Order::STATUS_DELIVERED])) {
                    $paymentStatus = Order::PAYMENT_PAID;
                } elseif ($status === Order::STATUS_CANCELLED) {
                    $paymentStatus = rand(0, 1) ? Order::PAYMENT_REFUNDED : Order::PAYMENT_PENDING;
                }

                // Random order items (1-4 products)
                $numItems = rand(1, 4);
                $orderProducts = $products->random($numItems);
                
                $subtotal = 0;
                $items = [];

                foreach ($orderProducts as $product) {
                    $quantity = rand(1, 3);
                    $unitPrice = $product->price;
                    $totalPrice = $unitPrice * $quantity;
                    $subtotal += $totalPrice;

                    $items[] = [
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'product_sku' => $product->sku,
                        'product_image' => $product->images->first()?->image,
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                        'total_price' => $totalPrice,
                    ];
                }

                // Apply random coupon sometimes
                $couponId = null;
                $couponCode = null;
                $discountAmount = 0;

                if (rand(0, 3) === 0 && $coupons->isNotEmpty()) {
                    $coupon = $coupons->random();
                    if ($subtotal >= $coupon->min_order_amount) {
                        $couponId = $coupon->id;
                        $couponCode = $coupon->code;
                        
                        if ($coupon->type === 'percentage') {
                            $discountAmount = $subtotal * ($coupon->value / 100);
                            if ($coupon->max_discount_amount) {
                                $discountAmount = min($discountAmount, $coupon->max_discount_amount);
                            }
                        } else {
                            $discountAmount = $coupon->value;
                        }
                    }
                }

                $shippingAmount = $subtotal >= 5000 ? 0 : ($shippingMethod === 'express' ? 150 : 80);
                $total = $subtotal - $discountAmount + $shippingAmount;

                // Create order with random date in last 6 months
                $createdAt = now()->subDays(rand(1, 180));

                $order = Order::create([
                    'order_number' => Order::generateOrderNumber(),
                    'user_id' => $customer->id,
                    'shipping_address_id' => $address->id,
                    'billing_address_id' => $address->id,
                    'status' => $status,
                    'payment_status' => $paymentStatus,
                    'payment_method' => $paymentMethod,
                    'transaction_id' => $paymentStatus === Order::PAYMENT_PAID ? 'TXN' . strtoupper(substr(uniqid(), -10)) : null,
                    'subtotal' => $subtotal,
                    'discount_amount' => $discountAmount,
                    'shipping_amount' => $shippingAmount,
                    'tax_amount' => 0,
                    'total' => $total,
                    'coupon_id' => $couponId,
                    'coupon_code' => $couponCode,
                    'shipping_method' => $shippingMethod,
                    'tracking_number' => in_array($status, [Order::STATUS_SHIPPED, Order::STATUS_DELIVERED]) ? 'TRK' . strtoupper(substr(uniqid(), -8)) : null,
                    'shipped_at' => in_array($status, [Order::STATUS_SHIPPED, Order::STATUS_DELIVERED]) ? $createdAt->copy()->addDays(rand(1, 3)) : null,
                    'delivered_at' => $status === Order::STATUS_DELIVERED ? $createdAt->copy()->addDays(rand(3, 7)) : null,
                    'customer_notes' => rand(0, 3) === 0 ? 'Please call before delivery.' : null,
                    'shipping_address_snapshot' => $address->toArray(),
                    'billing_address_snapshot' => $address->toArray(),
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);

                // Add order items
                foreach ($items as $item) {
                    $item['order_id'] = $order->id;
                    $item['created_at'] = $createdAt;
                    $item['updated_at'] = $createdAt;
                    OrderItem::create($item);
                }

                // Add status history
                $this->createStatusHistory($order, $status, $createdAt);

                $orderCount++;
            }
        }

        $this->command->info("Orders seeded: {$orderCount} total");
    }

    private function createStatusHistory(Order $order, string $currentStatus, $createdAt): void
    {
        $statusFlow = [
            Order::STATUS_PENDING => ['Pending' => 'Order placed'],
            Order::STATUS_CONFIRMED => ['Pending' => 'Order placed', 'Confirmed' => 'Payment confirmed'],
            Order::STATUS_PROCESSING => ['Pending' => 'Order placed', 'Confirmed' => 'Payment confirmed', 'Processing' => 'Order is being processed'],
            Order::STATUS_SHIPPED => ['Pending' => 'Order placed', 'Confirmed' => 'Payment confirmed', 'Processing' => 'Order is being processed', 'Shipped' => 'Order has been shipped'],
            Order::STATUS_DELIVERED => ['Pending' => 'Order placed', 'Confirmed' => 'Payment confirmed', 'Processing' => 'Order is being processed', 'Shipped' => 'Order has been shipped', 'Delivered' => 'Order delivered successfully'],
            Order::STATUS_CANCELLED => ['Pending' => 'Order placed', 'Cancelled' => 'Order cancelled by customer'],
        ];

        $flow = $statusFlow[$currentStatus] ?? ['Pending' => 'Order placed'];
        $dayOffset = 0;

        foreach ($flow as $status => $comment) {
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => strtolower($status),
                'comment' => $comment,
                'changed_by' => null,
                'created_at' => $createdAt->copy()->addDays($dayOffset),
                'updated_at' => $createdAt->copy()->addDays($dayOffset),
            ]);
            $dayOffset += rand(0, 2);
        }
    }
}
