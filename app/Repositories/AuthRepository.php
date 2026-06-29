<?php

namespace App\Repositories;

use App\Models\User;
class AuthRepository
{
    public function register(array $data, string $deviceType): array
    {
        $user = User::create($data);

        $token = $user->createToken(name: $deviceType, expiresAt: now()->addDays(3));

        return [
            "data" => $user,
            "token" => $token->plainTextToken
        ];
    }
}