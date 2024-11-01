<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddProductRequest;
use App\Http\Requests\OrderRequest;
use App\Http\Resources\ProductResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Models\Product;

class ProductController extends Controller
{
    public function addProduct(AddProductRequest $request): JsonResponse
    {
        $data = $request->validated();

        $path = $request->file('image')->store('images/products');

        $newProduct = Product::create([
            'name' => $data['name'],
            'price' => $data['price'],
            'image' => $path
        ]);

        return response()->json([
            'message' => 'Product added successfully!',
            'data' => [
                'id' => $newProduct->id,
                'name' => $newProduct->name,
                'price' => $newProduct->price,
                'image_url' => $request->getSchemeAndHttpHost() . '/storage/' . $newProduct->image
            ],
        ],201);
    }

    public function getAllProducts(): AnonymousResourceCollection
    {
        return ProductResource::collection(Product::all());
    }

    public function createOrder(OrderRequest $request): JsonResponse
    {
        $request->validated();

        return response()->json([
            'message' => 'Your order is successful please wait seller to respond!'
        ],201);
    }
}
