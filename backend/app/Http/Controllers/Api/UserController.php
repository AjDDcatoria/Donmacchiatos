<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SetUpUserRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $user_service;

    public function __construct(UserService $user_service)
    {
        $this->user_service = $user_service;
    }


    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): UserResource
    {
        $user = $request->user();
        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SetUpUserRequest $request): JsonResponse
    {
        $data = $request->validated();
        $this->user_service->updateUser($data,$request);

        return response()->json(['message' => 'Setup successful!'],201);
    }

}
