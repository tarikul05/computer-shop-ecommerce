<?php

namespace App\Modules\Order\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Order\Services\OrderService;
use App\Modules\Order\Requests\CheckoutRequest;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly OrderService $orderService
    ) {}

    /**
     * Get user orders
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 10);
        $orders = $this->orderService->getUserOrders($request->user(), $perPage);

        return $this->paginatedResponse($orders, function ($order) {
            return $this->orderService->formatOrder($order);
        });
    }

    /**
     * Get single order
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $order = $this->orderService->getUserOrder($request->user(), $id);

        if (!$order) {
            return $this->notFoundResponse('Order not found');
        }

        return $this->successResponse($this->orderService->formatOrder($order));
    }

    /**
     * Get order by order number
     */
    public function showByNumber(Request $request, string $orderNumber): JsonResponse
    {
        $order = $this->orderService->getByOrderNumber($orderNumber);

        if (!$order || $order->user_id !== $request->user()->id) {
            return $this->notFoundResponse('Order not found');
        }

        return $this->successResponse($this->orderService->formatOrder($order));
    }

    /**
     * Create order (checkout)
     */
    public function checkout(CheckoutRequest $request): JsonResponse
    {
        try {
            $result = $this->orderService->createFromCart(
                $request->user(),
                $request->validated()
            );

            return $this->createdResponse(
                $this->orderService->formatOrder($result['order']),
                $result['message']
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Cancel order
     */
    public function cancel(Request $request, int $id): JsonResponse
    {
        $order = $this->orderService->getUserOrder($request->user(), $id);

        if (!$order) {
            return $this->notFoundResponse('Order not found');
        }

        try {
            $order = $this->orderService->cancelOrder(
                $order,
                $request->input('reason'),
                $request->user()->id
            );

            return $this->successResponse(
                $this->orderService->formatOrder($order),
                'Order cancelled successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Track order
     */
    public function track(Request $request, string $orderNumber): JsonResponse
    {
        $order = $this->orderService->getByOrderNumber($orderNumber);

        if (!$order) {
            return $this->notFoundResponse('Order not found');
        }

        // Allow tracking without login (guest tracking)
        return $this->successResponse([
            'order_number' => $order->order_number,
            'status' => $order->status,
            'status_label' => $order->status_label,
            'payment_status' => $order->payment_status,
            'tracking_number' => $order->tracking_number,
            'shipped_at' => $order->shipped_at?->toISOString(),
            'delivered_at' => $order->delivered_at?->toISOString(),
            'status_history' => $order->statusHistory->map(fn ($h) => [
                'status' => $h->status,
                'comment' => $h->comment,
                'created_at' => $h->created_at->toISOString(),
            ]),
        ]);
    }
}
