<?php

namespace App\Jobs;

use App\Repositories\LinkRepository;
use App\Services\UserAgentParser;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Services\GeoLocation;
use App\Repositories\ClickRepository;
use Illuminate\Support\Facades\DB;

class RecordClick implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private string $slugLink,
        private string $userIp,
        private string $userAgent
    ) {}

    /**
     * Execute the job.
     */
    public function handle(GeoLocation $geoLocation, UserAgentParser $userAgent, ClickRepository $clickRepository, LinkRepository $linkRepository): void
    {
        $userIp = $this->userIp;
        $geo = $geoLocation->lookup($userIp);
        $userAgent = $userAgent->parse($this->userAgent);

        DB::transaction(function () use ($userIp, $geo, $userAgent, $clickRepository, $linkRepository) {
            $clickRepository->record($linkRepository->redirect($this->slugLink), $userIp, $geo, $userAgent);
            $linkRepository->incrementClicks($this->slugLink);
        });
    }
}
