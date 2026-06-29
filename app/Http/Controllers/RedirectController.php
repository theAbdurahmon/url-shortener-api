<?php

namespace App\Http\Controllers;

use App\Exceptions\BadRequestException;
use App\Exceptions\InvalidPasswordException;
use App\Http\Requests\UnlockLinkRequest;
use App\Jobs\RecordClick;
use App\Services\RedirectService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class RedirectController extends Controller
{
    public function __construct(private RedirectService $redirectService) {}

    private function recordClickJob(string $slug): void {
        RecordClick::dispatch($slug, request()->ip(), request()->userAgent());
    }

    public function __invoke(string $slug): RedirectResponse
    {
        $link = $this->redirectService->resolveBySlug($slug);
        $this->recordClickJob($slug);
        return redirect($link);
    }

    public function unlock(UnlockLinkRequest $request, string $slug): RedirectResponse|JsonResponse
    {
        try {
            $link = $this->redirectService->resolveBySlug($slug, $request->safe()->password);
            $this->recordClickJob($slug);
            return redirect($link);
        } catch (InvalidPasswordException $e) {
            return response()->json("Invalid password", 401);
        } catch (BadRequestException $e) {
            return response()->json("This link does not require a password", 422);
        }
    }
}