<?php

namespace App\Services;

use App\Exceptions\BadRequestException;
use App\Exceptions\InvalidPasswordException;
use App\Exceptions\LinkExpiredException;
use App\Exceptions\LinkInactiveException;
use App\Exceptions\RequiredPasswordLinkException;
use App\Repositories\LinkRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

class RedirectService
{
    public function __construct(
        private LinkRepository $linkRepository
    ) {
    }

    public function resolveBySlug(string $slug, ?string $password = null): string
    {
        if (!Cache::has("link:{$slug}")) {
            $link = $this->linkRepository->redirect($slug);
            $this->linkRepository->cacheLink($link);
        }

        $link = Cache::get("link:{$slug}");

        if ($password && $link["password"]) {
            if (!Hash::check($password, $link["password"])) {
                throw new InvalidPasswordException;
            }
        } elseif ($password && !$link["password"]) {
            throw new BadRequestException;
        } elseif (!$password && $link["password"]) {
            throw new RequiredPasswordLinkException;
        }

        if (!$link["is_active"]) {
            throw new LinkInactiveException;
        }

        if ($link["expires_at"] && $link["expires_at"] < date("Y-m-d")) {
            throw new LinkExpiredException;
        }

        if ($link["click_limit"] && $link["clicks_count"] >= $link["click_limit"]) {
            throw new LinkExpiredException;
        }

        return $link["original_url"];
    }
}