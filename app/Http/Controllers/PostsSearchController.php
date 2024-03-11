<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchPostRequest;
use App\Http\Resources\PostResource;
use App\Models\PostTranslation;

class PostsSearchController extends Controller
{
    public function search(SearchPostRequest $request)
    {
        return PostResource::collection(
            PostTranslation::when(
                $request->filled('title'),
                fn ($query) =>
                $query->where('title', 'like', '%' . $request->validated('query') . '%')
            )
                ->latest()->get()
        )
//            ->getIterator()
        ;
    }
}
