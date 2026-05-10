<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;

#[OA\Info(
    title: 'Translation Management API',
    description: 'API for searching, exporting and storing translations with Sanctum authentication.',
    version: '1.0.0'
)]
#[OA\Server(
    url: 'http://localhost/api',
    description: 'Local development server'
)]
#[OA\SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'Bearer'
)]
class AuthController extends Controller
{
    use ApiResponser;

    #[OA\Post(
        path: '/v1/login',
        tags: ['Auth'],
        summary: 'Authenticate a user and receive an access token.',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'email', type: 'string', format: 'email'),
                    new OA\Property(property: 'password', type: 'string'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Authenticated successfully.'),
            new OA\Response(response: 401, description: 'Invalid credentials provided.'),
        ]
    )]
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt($credentials)) {
            return $this->errorResponse('Invalid credentials provided.', 401);
        }

        $user = Auth::user();
        
        $token = $user->createToken('access_token')->plainTextToken;

        return $this->successResponse([
            'user'  => $user,
            'token' => $token,
        ], 'Authenticated successfully.');
    }

    #[OA\Post(
        path: '/v1/logout',
        tags: ['Auth'],
        security: [['bearerAuth' => []]],
        summary: 'Log out the authenticated user by revoking the current token.',
        responses: [
            new OA\Response(response: 200, description: 'Logged out successfully.'),
            new OA\Response(response: 401, description: 'Authentication required.'),
        ]
    )]
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse(null, 'Logged out successfully.');
    }
}
