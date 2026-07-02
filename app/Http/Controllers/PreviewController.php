<?php

namespace App\Http\Controllers;

use App\Repositories\LinkRepository;

class PreviewController extends Controller
{
    public function __construct(
        private LinkRepository $linkRepository,
    ) {
    }


    public function __invoke(string $slug)
    {
        $currentUrl = env("APP_URL") . "/api/" . $slug;
        $link = $this->linkRepository->redirect($slug);
        return view("link_preview", compact("link", "currentUrl"));
    }
}
