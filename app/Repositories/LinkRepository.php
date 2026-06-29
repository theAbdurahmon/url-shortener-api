<?php
namespace App\Repositories;

use App\Models\Link;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class LinkRepository
{
    public function cacheLink(Link $link): void
    {
        Cache::put("link:{$link->slug}", [
            'id' => $link->id,
            'original_url' => $link->original_url,
            'is_active' => $link->is_active,
            'expires_at' => $link->expires_at,
            'password' => $link->password,
            'click_limit' => $link->click_limit,
            'clicks_count' => $link->clicks_count
        ], now()->addHours(24));
    }

    public function create(array $data, User $currentAuthUser): Link
    {
        $link = $currentAuthUser->links()->create($data);
        $this->cacheLink($link);
        return $link;
    }

    public function getAll(User $currentAuthUser): Collection
    {
        return $currentAuthUser->links;
    }

    public function get(string $slug, User $currentAuthUser): Link
    {
        return Link::where("user_id", $currentAuthUser["id"])->where("slug", $slug)->firstOrFail();
    }

    public function update(array $data, string $slug, User $currentAuthUser): Link
    {
        $link = $this->get($slug, $currentAuthUser);
        $link->update($data);
        $link->refresh();

        $this->cacheLink($link);

        return $link;
    }

    public function delete(string $slug, User $currentAuthUser): void
    {
        $this->get($slug, $currentAuthUser)->delete();
        Cache::forget("link:{$slug}");
    }

    public function incrementClicks(string $slug): void
    {
        $this->redirect($slug)->increment("clicks_count");
    }

    public function redirect($slug): Link
    {
        return Link::where("slug", $slug)->firstOrFail();
    }
}