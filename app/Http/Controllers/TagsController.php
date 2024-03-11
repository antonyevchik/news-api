<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexTagsRequest;
use App\Http\Requests\StoreTagRequest;
use App\Http\Requests\UpdateTagRequest;
use App\Http\Resources\TagResource;
use App\Models\Tag;

class TagsController extends Controller
{
    public function index(IndexTagsRequest $request)
    {
        return TagResource::collection(
            Tag::when(
                $request->filled('post_id'),
                fn ($query) =>
                $query->whereHas('posts', fn ($query) =>
                    $query->where('id', $request->validated('post_id')))
            )
                ->latest()
                ->paginate(
                    $request->validated('per_page', 10) > 50 ? 50 : $request->validated('per_page'),
                    ['*'],
                    'tags',
                    $request->validated('page', 1)
                )
        );
    }

    public function store(StoreTagRequest $request)
    {
        return TagResource::make(Tag::create($request->validated()));
    }

    public function update(UpdateTagRequest $request, Tag $tag)
    {
        $tag->update($request->validated());

        return response()->json(['message' => 'Tag name updated!'], 201);
    }

    public function destroy(Tag $tag)
    {
        $tag->delete();

        return response()->json(['message' => 'Tag deleted'], 204);
    }
}
