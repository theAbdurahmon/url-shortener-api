<?php

namespace App\Services;

use App\Exceptions\BadRequestException;
use App\Repositories\AuthRepository;
use Illuminate\Support\Facades\Auth;

class AuthenticationService
{
    public function __construct(
        private AuthRepository $authRepository
    ) {
    }

    public function checkUser(array $data, string $deviceType): array
    {
        if (!Auth::once($data)) {
            throw new BadRequestException;
        }

        $user = Auth::user();

        $token = $user->createToken(name: $deviceType, expiresAt: now()->addDays(3));

        return [
            "data" => $user,
            "token" => $token->plainTextToken
        ];
    }
}