<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddProductRequest;
use App\Http\Requests\EditProductRequest;
use App\Http\Resources\ProductResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Models\Product;

class ProductController extends Controller
{
    public function addProduct(AddProductRequest $request): JsonResponse
    {
        $newProduct = $request->createProduct();
        // Todo email all user that there is a new product.

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

    public function editProduct(EditProductRequest $request): JsonResponse
    {
        $data = $request->validated();

        if(!str_contains($data['image_url'],$request->getSchemeAndHttpHost())) {
            $data['image_url'] = $request->file('image_url')->store('images/products');
            $newProduct = Product::updateOrCreate(
                ['id' => $data['id']],
                [
                    'name' => $data['name'],
                    'image' =>  $data['image_url'],
                    'price' => $data['price']
                ]
            );

            return response()->json([
                'message' => 'Product edit successfully!',
                'data' => [
                    'id' => $newProduct->id,
                    'name' => $newProduct->name,
                    'price' => $newProduct->price,
                    'image_url' => $request->getSchemeAndHttpHost() . '/storage/' . $newProduct->image
                ],
            ],201);
        } else {
            $newProduct = Product::updateOrCreate(
                ['id' => $data['id']],
                [
                    'name' => $data['name'],
                    'price' => $data['price']
                ]
            );
            return response()->json([
                'message' => 'Product edit successfully!',
                'data' => [
                    'id' => $newProduct->id,
                    'name' => $newProduct->name,
                    'price' => $newProduct->price,
                    'image_url' => $data['image_url'],
                ],
            ],201);
        }
    }

    public function getAllProducts(): AnonymousResourceCollection
    {
        return ProductResource::collection(Product::all());
    }
}
