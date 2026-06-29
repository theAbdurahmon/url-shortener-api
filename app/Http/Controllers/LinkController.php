<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLinkRequest;
use App\Http\Requests\UpdateLinkRequest;
use App\Repositories\LinkRepository;
use App\Services\LinkService;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;

class LinkController extends Controller
{
    public function __construct(
        private LinkService $linkService,
        private LinkRepository $linkRepository
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): ResourceCollection
    {
        return $this->linkRepository->getAll($this->currentAuthUser())->toResourceCollection();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLinkRequest $request): JsonResource
    {
        return $this->linkService->create($request->safe()->all(), $this->currentAuthUser())->toResource();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResource
    {
        return $this->linkRepository->get($id, $this->currentAuthUser())->toResource();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLinkRequest $request, string $slug): JsonResource
    {
        return $this->linkService->update($request->safe()->all(), $slug, $this->currentAuthUser())->toResource();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $slug): Response
    {
        $this->linkRepository->delete($slug, $this->currentAuthUser());
        return response()->noContent();
    }
}
