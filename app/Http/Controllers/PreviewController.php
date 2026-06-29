<?php

namespace App\Http\Controllers;

use App\Repositories\LinkRepository;
use App\Services\DateFormat;

class PreviewController extends Controller
{
    public function __construct(
        private LinkRepository $linkRepository,
        private DateFormat $dateFormat
    ) {}


    public function __invoke(string $slug)
    {
        $currentUrl = env("APP_URL") . "/api/" . $slug;
        $link = $this->linkRepository->redirect($slug);
        return view("link_preview", compact("link", "currentUrl"));
    }
}
