<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLinkRequest;
use App\Http\Requests\UpdateLinkRequest;
use App\Services\LinkService;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;

class LinkController extends Controller
{
    public function __construct(
        private LinkService $linkService
        ){}
    /**
     * Display a listing of the resource.
     */
    public function index(): ResourceCollection
    {
        return $this->linkService->getAllData()->toResourceCollection();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLinkRequest $request): JsonResource
    {
        return $this->linkService->create($request->safe()->all())->toResource();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResource
    {
        return $this->linkService->getLink($id)->toResource();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLinkRequest $request, string $id): JsonResource
    {
        return $this->linkService->update($request->safe()->all(), $id)->toResource();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): Response
    {
        $this->linkService->delete($id);
        return response()->noContent();
    }
}
