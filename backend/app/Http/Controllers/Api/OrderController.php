<?php

namespace App\Http\Controllers\Api;

use App\Constants\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateOrderRequest;
use App\Http\Requests\ShowOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class OrderController extends Controller
{
    /**
     * @var OrderService $service The service instance for handling order-related logic.
     */
    protected OrderService $order_service;

    /**
     * OrderController constructor.
     *
     * @param OrderService $order_service
     */
    public function __construct(OrderService $order_service)
    {
        $this->order_service = $order_service;
    }

    /**
     * Creates a new order based on validated user input.
     *
     * @param CreateOrderRequest $request The validated request object containing order data.
     * @return JsonResponse A JSON response indicating order success, including the order data.
     */
    public function create(CreateOrderRequest $request): JsonResponse
    {
        $data = $request->validated(); // Validate inputs
        $current_user = $request->user(); // Retrieve the currently authenticated
        $new_order = $this->order_service->createOrder($data,$current_user); // Create a new order

        // Todo send admin a notification that there is a new order.

        return response()->json([
            'message' => "Your order is successful. wait seller to respond!",
            'data' => new OrderResource($new_order->load('items'))
        ],201);
    }

    /**
     * Retrieve orders base on the user's role and specified status filter.
     *
     * This method retrieves orders based on the authenticated user's role and specified status.
     * Admin users can view all orders, whereas other users will only see their own orders filtered by status.
     *
     * @param ShowOrderRequest $request - A custom request object that validates the
     *                                  `view_scope` (user role) and `status` filter
     *
     * @return JsonResponse - Returns a JSON response with a success message and a list of filtered orders.
     *
     * ### Authorization
     * - If `view_scope` is set to Role::ADMIN, the `viewAny` policy is enforced on the Order model to ensure
     *   only admin can view all orders.
     */
    public function show(ShowOrderRequest $request): JsonResponse
    {
        // Validated filters for order viewing scope and status
        $filter = $request->validated();

        // Authorize admin view of all orders if user has 'admin' scope
        if($filter['view_scope'] === Role::ADMIN) {
            Gate::authorize('viewAny', Order::class);
        }

        // Fetch filtered orders based on user role and order status
        $filtered_order = $this->order_service->getOrdersByStatus($filter, $request->user());

        // Return success message and filtered orders in JSON format
        return response()->json([
            'message' => 'Get ' .  $filter['status'] . ' orders successful!',
            'data' => OrderResource::collection($filtered_order)
        ]);
    }
}
