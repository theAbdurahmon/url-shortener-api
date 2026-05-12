<?php
namespace App\Repositories;

use App\Models\Link;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
class LinkRepository {
    public function create(array $data): Link {
        $link = Link::create($data);
        Cache::put("link:{$data['slug']}", [
            'id' => $link->id,
            'original_url' => $link->original_url,
            'is_active' => $link->is_active,
            'expires_at' => $link->expires_at,
            'password' => $link->password,
            'click_limit' => $link->click_limit,
            'clicks_count' => $link->clicks_count
        ], now()->addHours(24));
        return $link;
    }

    public function getAllData(): Collection {
        return Link::all();
    }

    public function get(int $id): Link {
         return Link::findOrFail($id);
    }

    public function update(array $data, int $idOfLink): Link {
        $link = Link::findOrFail($idOfLink);
        $link->update($data);
        $link->refresh();

        Cache::put("link:{$link->slug}", [
            'id' => $link->id,
            'original_url' => $link->original_url,
            'is_active' => $link->is_active,
            'expires_at' => $link->expires_at,
            'password' => $link->password,
            'click_limit' => $link->click_limit,
            'clicks_count' => $link->clicks_count,
        ], now()->addHours(24));

        return $link;
    }

    public function delete(int $idOfLink): void {
         $link = Link::findOrFail($idOfLink);
         Cache::forget("link{$link->slug}");
         $link->delete();
    }
}