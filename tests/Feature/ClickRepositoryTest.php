<?php
namespace Tests\Feature\Repositories;

use App\Models\Click;
use App\Models\Link;
use App\Repositories\ClickRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClickRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private ClickRepository $clickRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->clickRepository = app(ClickRepository::class);
    }

    public function test_get_stats_returns_correct_counts(): void
    {
        $link = Link::factory()->create();

        Click::factory()->count(3)->create([
            'link_id' => $link->id,
            'ip_address' => '1.1.1.1',
            'clicked_at' => now(),
        ]);

        Click::factory()->create([
            'link_id' => $link->id,
            'ip_address' => '2.2.2.2',
            'clicked_at' => now(),
        ]);

        $stats = $this->clickRepository->getStats($link->id);

        $this->assertEquals(4, $stats['total_clicks']);
        $this->assertEquals(2, $stats['unique_clicks']);
        $this->assertEquals(4, $stats['clicks_today']);
    }

    public function test_get_timeline_returns_clicks_grouped_by_day(): void
    {
        $link = Link::factory()->create();

        Click::factory()->count(3)->create([
            'link_id' => $link->id,
            'clicked_at' => '2026-06-20 10:00:00',
        ]);

        Click::factory()->count(2)->create([
            'link_id' => $link->id,
            'clicked_at' => '2026-06-21 15:00:00',
        ]);

        $result = $this->clickRepository->getTimeline($link->id, '2026-06-20', '2026-06-21');

        $this->assertCount(2, $result);
        $this->assertEquals(3, $result[0]['clicks']);
        $this->assertEquals(2, $result[1]['clicks']);
    }

    public function test_get_timeline_excludes_clicks_outside_range(): void
    {
        $link = Link::factory()->create();

        Click::factory()->create([
            'link_id' => $link->id,
            'clicked_at' => '2026-05-01 10:00:00',
        ]);

        $result = $this->clickRepository->getTimeline($link->id, '2026-06-01', '2026-06-30');

        $this->assertEmpty([]);
    }
}