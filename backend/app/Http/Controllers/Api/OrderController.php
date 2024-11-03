<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateOrderRequest;
use App\Http\Resources\OrderResource;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;

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
}
