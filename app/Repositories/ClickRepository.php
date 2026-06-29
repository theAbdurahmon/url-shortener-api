<?php

namespace App\Repositories;

use App\Models\Click;
use App\Models\Link;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ClickRepository
{
    public function record(Link $link, string $userIp, mixed $geo, Agent|array $userAgent): void
    {
        Click::create([
            "link_id" => $link->id,
            "ip_address" => $userIp,
            "country" => $geo->location->country_code,
            "city" => $geo->location->city,
            "device_type" => $userAgent->deviceType(),
            "browser" => $userAgent->browser(),
            "os" => $userAgent->platform(),
            "referer" => $link->original_url,
        ]);
    }

    private function getTopCountry(int $linkId): ?string
    {
        return Click::where('link_id', $linkId)
            ->select('country', DB::raw('COUNT(*) as total'))
            ->groupBy('country')
            ->orderByDesc('total')
            ->value('country');
    }

    private function getTopDevice(int $linkId): ?string
    {
        return Click::where('link_id', $linkId)
            ->select('device_type', DB::raw('COUNT(*) as total'))
            ->groupBy('device_type')
            ->orderByDesc('total')
            ->value('device_type');
    }

    public function getStats(int $linkId): array
    {
        $click = Click::where("link_id", $linkId);
        $stats = $click
            ->selectRaw('
                COUNT(*) as total_clicks,
                COUNT(DISTINCT ip_address) as unique_clicks,
                SUM(CASE WHEN clicked_at::date = CURRENT_DATE THEN 1 ELSE 0 END) as clicks_today,
                SUM(CASE WHEN clicked_at BETWEEN ? AND ? THEN 1 ELSE 0 END) as clicks_week
             ', [now()->startOfWeek(), now()->endOfWeek()])->first();

        return [
            'total_clicks' => $stats->total_clicks,
            'unique_clicks' => $stats->unique_clicks,
            'clicks_today' => $stats->clicks_today,
            'clicks_week' => $stats->clicks_week,
            'top_country' => $this->getTopCountry($linkId),
            'topDevice' => $this->getTopDevice($linkId)
        ];
    }

    public function getTimeline(int $linkId, string $from, string $to): array
    {
        return Click::where("link_id", $linkId)
            ->whereBetween('clicked_at', [
                Carbon::parse($from)->startOfDay(),
                Carbon::parse($to)->endOfDay()
            ])
            ->selectRaw("
            clicked_at::date as date, COUNT(*) as clicks,
            COUNT(DISTINCT ip_address) as unique_clicks 
            ")
            ->groupByRaw("clicked_at::date")
            ->orderBy("date")
            ->get()
            ->map(fn($row) => [
                "date" => $row->date,
                "clicks" => $row->clicks,
                "unique_clicks" => $row->unique_clicks,
                "topCountry" => $this->getTopCountry($linkId),
                "topDevice" => $this->getTopDevice($linkId)
            ])->toArray();
    }
}