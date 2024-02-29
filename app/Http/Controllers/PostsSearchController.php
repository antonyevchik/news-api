<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchPostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;

class PostsSearchController extends Controller
{
    public function search(SearchPostRequest $request)
    {
        return PostResource::collection(Post::get())->getIterator();
    }
}
