<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexTagsRequest;
use App\Http\Resources\TagResource;
use App\Models\Tag;
use Illuminate\Http\Request;

class TagsController extends Controller
{
    public function index(IndexTagsRequest $request)
    {
        return TagResource::collection(
            Tag::when(
                $request->filled('post_id'),
                fn($query) =>
                $query->whereHas('posts', fn($query) =>
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
}
