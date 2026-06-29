<?php
namespace Tests\Unit\Jobs;

use App\Jobs\RecordClick;
use App\Repositories\ClickRepository;
use App\Repositories\LinkRepository;
use App\Services\GeoLocation;
use App\Services\UserAgentParser;
use Mockery;
use Tests\TestCase;
use App\Models\Link;

class RecordClickJobTest extends TestCase
{
    public function test_handle_records_click_successfully(): void
    {
        $geoLocation = Mockery::mock(GeoLocation::class);
        $userAgentParser = Mockery::mock(UserAgentParser::class);
        $clickRepository = Mockery::mock(ClickRepository::class);
        $linkRepository = Mockery::mock(LinkRepository::class);

        $geoLocation->shouldReceive('lookup')
            ->with('127.0.0.1')
            ->andReturn(['country' => 'UZ', 'city' => 'Tashkent']);

        $userAgentParser->shouldReceive('parse')
            ->andReturn(['type' => 'mobile', 'browser' => 'Chrome', 'os' => 'Android']);

        $link = Link::factory()->make(["id" => 1]);

        $linkRepository->shouldReceive('redirect')->with("abc123")->andReturn($link);

        $clickRepository->shouldReceive('record')->once();
        $linkRepository->shouldReceive('incrementClicks')->once();

        $job = new RecordClick('abc123', '127.0.0.1', 'Mozilla/5.0...');
        $job->handle($geoLocation, $userAgentParser, $clickRepository, $linkRepository);
    }
}