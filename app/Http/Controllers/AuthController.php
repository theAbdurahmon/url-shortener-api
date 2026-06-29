<?php

namespace App\Http\Controllers;

use App\Exceptions\BadRequestException;
use App\Http\Requests\AuthUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\UserResource;
use App\Repositories\AuthRepository;
use App\Services\AuthenticationService;
use App\Services\UserAgentParser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AuthController extends Controller
{

    public function __construct(
        private UserAgentParser $userAgent,
        private AuthenticationService $authenticationService,
        private AuthRepository $authRepository,
    ) {
    }

    private function deviceType(): string
    {
        return $this->userAgent->parse(request()->userAgent())->deviceType();
    }

    public function create(StoreUserRequest $request): JsonResource
    {
        $user = $this->authRepository->register($request->safe()->all(), $this->deviceType());

        return new UserResource($user["data"], $user["token"]);
    }

    public function login(AuthUserRequest $request): JsonResource|JsonResponse
    {
        try {
            $user = $this->authenticationService->checkUser($request->safe()->all(), $this->deviceType());

            return new UserResource($user["data"], $user["token"]);
        } catch (BadRequestException $e) {
            return response()->json("Invalid email or password", 401);
        }
    }

    public function logout(Request $request): Response
    {
        $request->user()->currentAccessToken()->delete();

        return response()->noContent();
    }
}