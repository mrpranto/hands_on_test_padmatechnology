<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AuthServices;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(AuthServices $services)
    {
        $this->service = $services;
    }

    public function login(Request $request): JsonResponse
    {
        return $this->service
            ->validateLogin($request)
            ->getLoginResponse($request);
    }

    public function logout(Request $request): JsonResponse
    {
        auth()->user()->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout Successful'
        ], 201);
    }
}
