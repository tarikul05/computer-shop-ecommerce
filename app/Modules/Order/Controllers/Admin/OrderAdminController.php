<?php

namespace App\Modules\Order\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Order\Services\OrderService;
use App\Modules\Order\Requests\UpdateOrderStatusRequest;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderAdminController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly OrderService $orderService
    ) {}

    /**
     * Get all orders (paginated)
     */
    public function index(Request $request): JsonResponse
    {
        $filters = [
            'status' => $request->input('status'),
            'payment_status' => $request->input('payment_status'),
            'user_id' => $request->input('user_id'),
            'date_from' => $request->input('date_from'),
            'date_to' => $request->input('date_to'),
            'search' => $request->input('search'),
            'sort_by' => $request->input('sort_by', 'created_at'),
            'sort_dir' => $request->input('sort_dir', 'desc'),
        ];

        $perPage = $request->input('per_page', 15);
        $orders = $this->orderService->getPaginated($filters, $perPage);

        return $this->paginatedResponse($orders, function ($order) {
            return $this->orderService->formatOrder($order);
        });
    }

    /**
     * Get single order
     */
    public function show(int $id): JsonResponse
    {
        $order = $this->orderService->getById($id);

        if (!$order) {
            return $this->notFoundResponse('Order not found');
        }

        return $this->successResponse($this->orderService->formatOrder($order));
    }

    /**
     * Update order status
     */
    public function updateStatus(UpdateOrderStatusRequest $request, int $id): JsonResponse
    {
        $order = $this->orderService->getById($id);

        if (!$order) {
            return $this->notFoundResponse('Order not found');
        }

        try {
            $order = $this->orderService->updateStatus(
                $order,
                $request->status,
                $request->comment,
                $request->user()->id
            );

            return $this->successResponse(
                $this->orderService->formatOrder($order),
                'Order status updated'
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Update payment status
     */
    public function updatePaymentStatus(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'payment_status' => 'required|in:pending,paid,failed,refunded',
            'transaction_id' => 'nullable|string',
        ]);

        $order = $this->orderService->getById($id);

        if (!$order) {
            return $this->notFoundResponse('Order not found');
        }

        $order = $this->orderService->updatePaymentStatus(
            $order,
            $request->payment_status,
            $request->transaction_id
        );

        return $this->successResponse(
            $this->orderService->formatOrder($order),
            'Payment status updated'
        );
    }

    /**
     * Update tracking info
     */
    public function updateTracking(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'tracking_number' => 'required|string|max:100',
        ]);

        $order = $this->orderService->getById($id);

        if (!$order) {
            return $this->notFoundResponse('Order not found');
        }

        $order = $this->orderService->updateTracking($order, $request->tracking_number);

        return $this->successResponse(
            $this->orderService->formatOrder($order),
            'Tracking number updated'
        );
    }

    /**
     * Cancel order
     */
    public function cancel(Request $request, int $id): JsonResponse
    {
        $order = $this->orderService->getById($id);

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
                'Order cancelled'
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Get order statistics
     */
    public function statistics(Request $request): JsonResponse
    {
        $filters = [
            'date_from' => $request->input('date_from'),
            'date_to' => $request->input('date_to'),
        ];

        $stats = $this->orderService->getStatistics($filters);

        return $this->successResponse($stats);
    }
}
