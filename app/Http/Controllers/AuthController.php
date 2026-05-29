<?php

namespace App\Http\Controllers;

use App\Data\UserData;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $payload = $request->validated();

        $user = $this->userRepository->existsBy('email', $payload['email']);

        if (! $user) {
            $this->userRepository->create(UserData::from($payload));
        }

        return $this
            ->message('Registered successfully. Please log in.')
            ->createdResponse();
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $payload = $request->validated();

        $user = $this->userRepository->findBy('email', $payload['email']);

        if (! $user || ! Hash::check($payload['password'], $user->password)) {
            return $this
                ->message('Invalid credentials.')
                ->unauthorizedResponse();
        }

        $token = $user
            ->createToken(config('auth.sanctum_token_prefix'))
            ->plainTextToken;

        $user->refresh();

        return $this
            ->message('Logged in successfully.')
            ->data([
                'user' => new UserResource($user),
                'token' => $token,
                'token_type' => 'Bearer',
            ])
            ->successResponse();
    }
}
