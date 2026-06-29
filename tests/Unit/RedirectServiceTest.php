<?php
namespace Tests\Unit\Services;

use App\Exceptions\LinkExpiredException;
use App\Exceptions\LinkInactiveException;
use App\Exceptions\InvalidPasswordException;
use App\Repositories\LinkRepository;
use App\Services\RedirectService;
use Illuminate\Support\Facades\Cache;
use Mockery;
use Tests\TestCase;

class RedirectServiceTest extends TestCase
{
    private RedirectService $redirectService;
    private $linkRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->linkRepository = Mockery::mock(LinkRepository::class);
        $this->redirectService = new RedirectService($this->linkRepository);
    }

    public function test_resolve_by_slug_returns_original_url(): void
    {
        $cached = [
            'id' => 1,
            'original_url' => 'https://google.com',
            'is_active' => true,
            'expires_at' => null,
            'password' => null,
            'click_limit' => null,
            'clicks_count' => 0,
        ];

        Cache::shouldReceive('has')->with('link:abc123')->andReturn(true);
        Cache::shouldReceive('get')->with('link:abc123')->andReturn($cached);

        $result = $this->redirectService->resolveBySlug('abc123');

        $this->assertEquals('https://google.com', $result);
    }

    public function test_inactive_link_throws_exception(): void
    {
        $cached = [
            'is_active' => false,
            'expires_at' => null,
            'password' => null,
            'click_limit' => null,
            'clicks_count' => 0,
        ];

        Cache::shouldReceive('has')->with('link:abc123')->andReturn(true);
        Cache::shouldReceive('get')->with('link:abc123')->andReturn($cached);

        $this->expectException(LinkInactiveException::class);
        $this->redirectService->resolveBySlug('abc123');
    }

    public function test_expired_link_throws_exception(): void
    {
        $cached = [
            'is_active' => true,
            'expires_at' => now()->subDay()->toDateString(),
            'password' => null,
            'click_limit' => null,
            'clicks_count' => 0,
        ];

        Cache::shouldReceive('has')->with('link:abc123')->andReturn(true);
        Cache::shouldReceive('get')->with('link:abc123')->andReturn($cached);

        $this->expectException(LinkExpiredException::class);
        $this->redirectService->resolveBySlug('abc123');
    }

    public function test_wrong_password_throws_exception(): void
    {
        $cached = [
            'is_active' => true,
            'expires_at' => null,
            'password' => bcrypt('correct'),
            'click_limit' => null,
            'clicks_count' => 0,
        ];

        Cache::shouldReceive('has')->with('link:abc123')->andReturn(true);
        Cache::shouldReceive('get')->with('link:abc123')->andReturn($cached);

        $this->expectException(InvalidPasswordException::class);
        $this->redirectService->resolveBySlug('abc123', 'wrong');
    }

    public function test_click_limit_exceeded_throws_exception(): void
    {
        $cached = [
            'is_active' => true,
            'expires_at' => null,
            'password' => null,
            'click_limit' => 5,
            'clicks_count' => 5,
        ];

        Cache::shouldReceive('has')->with('link:abc123')->andReturn(true);
        Cache::shouldReceive('get')->with('link:abc123')->andReturn($cached);

        $this->expectException(LinkExpiredException::class);
        $this->redirectService->resolveBySlug('abc123');
    }
}