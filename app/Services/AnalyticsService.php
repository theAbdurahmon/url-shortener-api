<?php

namespace App\Services;

use App\Repositories\ClickRepository;
use App\Repositories\LinkRepository;
use App\Models\User;

class AnalyticsService
{
    public function __construct(
        private LinkRepository $linkRepository,
        private ClickRepository $clickRepository
    ) {
    }
    public function getStats(string $slug, User $currentAuthUser): array
    {
        $link = $this->linkRepository->get($slug, $currentAuthUser);
        $stats = $this->clickRepository->getStats($link->id);
        $stats["created_at"] = $link->created_at;

        return $stats;
    }
}