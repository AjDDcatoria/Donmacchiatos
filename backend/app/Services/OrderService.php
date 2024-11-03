<?php

namespace App\Services;

use App\Constants\Role;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

class OrderService
{
    /**
     * Creates a new order based on the provided data and user information.
     *
     * This method calculates the total price for each product in the cart,
     * aggregates the total order price, and prepares the order data to be stored.
     * It then calls the storeOrder() method to save the order.
     *
     * @param array $data Array containing cart items and payment information.
     * @param object $user The user object creating the order.
     * @return Order The created order with its associated items.
     */
    public function createOrder(array $data,object $user): Order
    {
        // Extract products IDs from the cart data to fetch product details from the database
        $productsIds = array_column($data['cart'],'id');

        // Retrieve all products that match the IDs from the cart
        $products = Product::whereIn('id',$productsIds)->get();

        // Initialize an empty array to hold ordered products and a variable for the grand total price
        $ordered_products = [];
        $grand_total = 0;

        foreach($data['cart'] as $cartItem) {
            // Find the corresponding product from the retrieved products by matching the ID
            $product = $products->firstWhere('id',$cartItem['id']);

            // If the product exists, calculate the total price for this item
            if($product) {
                // Calculate total price for this item by multiplying product price and quantity
                $totalPrice = $product->price * ($cartItem['quantity'] ?? 1);

                // Add product details, quantity, and total price to the ordered products array
                $ordered_products[] = [
                  'id' => $product->id,
                  'quantity' => $cartItem['quantity'] ?? 1,
                  'total_price' => $totalPrice
                ];

                // Add the item's total price to grand total
                $grand_total += $totalPrice;
            }
        }

        // Prepare order data with ordered products, payment code, grand total, user ID, and an optional message
        $order = [
            'orders' => $ordered_products,
            'payment' => $data['payment']['code'],
            'grand_total' => $grand_total,
            'user_id' => $user->id,
            'message' => $data['message'] ?? null
        ];

        // Call storeOrder to save the order in the database and return the result
        return $this->storeOrder($order);
    }

    /**
     * Stores an order and its associated items in the database.
     *
     * This method creates a new order record with the given data and then
     * iterates through each item to create individual order items associated with the order.
     *
     * @param array $order Array containing order details, including items, user ID, payment code, and total price.
     * @return Order The saved order with its items.
     */
    public function storeOrder(array $order): Order
    {
        // Extract individual pieces of order data for easier reference
        $order_data = $order['orders'];
        $grand_total = $order['grand_total'];
        $user_id = $order['user_id'];
        $message = $order['message'];
        $payment = $order['payment'];

        // Create a new order record in the database with user ID, payment, grand total, and optional message
        $order = Order::create([
           'user_id' => $user_id,
           'payment' => $payment,
           'grand_total' => $grand_total,
           'message' => $message
        ]);

        // Loop through each item in the order data to create individual order items
        foreach ($order_data as $order_item_data)
        {
            $order->items()->create([
               'product_id' => $order_item_data['id'],
               'quantity' => $order_item_data['quantity'],
               'total_price' => $order_item_data['total_price']
            ]);
        }

        // Retrieve and return the order along with its items using the getOrderItemById method
        return $this->getOrderItemById($order->id);
    }

    /**
     * Retrieves an order along with its items by order ID.
     *
     * This method fetches an order with its associated items using the provided order ID.
     *
     * @param string $id The ID of the order to retrieve.
     * @return Order|null The order with its items if found, or null if not found.
     */
    public function getOrderItemById(string $id): ?Order
    {
        // Find the order by ID and eager load its items relationship
        return Order::with('items')->find($id);
    }

    /**
     * Retrieve orders filtered by status and user role.
     *
     * This method constructs a query to retrieve orders based on the specified `status`
     * and the role of the user. Admin can access all orders, while customers are
     * limited to viewing only their own orders filtered by status.
     *
     * @param array $filter - Associative array containing the filter parameters:
     *    - `status` - Order status (e.g., 'pending', 'completed', 'all') to filter the results.
     *    - `view_scope` - Specifies the user's view scope, such as `Role::ADMIN` or `Role::CUSTOMER`.
     * @param object $user - The authenticated user object; provides `id` for customer filtering.
     * @return Collection - Collection of orders that match the provided criteria.
     *
     * ### Query Logic
     * - If `status` is set to any value other than 'all', the query filters orders by that status.
     * - If `view_scope` is not `Role::ADMIN`, the query filters orders to include only
     *   those belonging to the authenticated user.
     *
     * ### Example Usage
     * ```php
     * $orders = $orderService->getOrdersByStatus(['status' => 'pending', 'view_scope' => Role::CUSTOMER], $user);
     * ```
     */
    public function getOrdersByStatus(array $filter,object $user): Collection
    {
        // Initialize the order query with user and item relationships
        $orderQuery = Order::with(['user', 'items']);

        // Filter by order status if not requesting all orders
        if ($filter['status'] !== 'all') {
            $orderQuery->where('status', $filter['status']);
        }

        // If the user is a customer, restrict the query to their orders only
        if ($filter['view_scope'] !== Role::ADMIN) {
            $orderQuery->where('user_id', $user->id);
        }

        // Execute the query and return the collection of orders
        return $orderQuery->get();
    }
}
