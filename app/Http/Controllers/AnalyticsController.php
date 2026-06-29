<?php

namespace App\Http\Controllers;

use App\Http\Requests\TimelineRequest;
use App\Repositories\ClickRepository;
use App\Repositories\LinkRepository;
use App\Services\AnalyticsService;
use Illuminate\Http\JsonResponse;

class AnalyticsController extends Controller
{
    public function __construct(
        private AnalyticsService $analyticsService,
        private LinkRepository $linkRepository,
        private ClickRepository $clickRepository
    ) {
    }

    public function stats(string $slug): JsonResponse
    {
        $stats = $this->analyticsService->getStats($slug, $this->currentAuthUser());
        return response()->json($stats);
    }

    public function timeline(TimelineRequest $timelineRequest, string $slug): JsonResponse
    {
        $link = $this->linkRepository->get($slug, $this->currentAuthUser());
        $data = $this->clickRepository->getTimeline($link->id, $timelineRequest->safe()->from, $timelineRequest->safe()->to);

        return response()->json(["data" => $data]);
    }
}