<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchPostRequest;
use App\Models\Post;
use App\Services\PostAggregate;
use Illuminate\Http\Request;

class PostsSearchController extends Controller
{
    public function search(SearchPostRequest $request)
    {
        $posts = Post::get()->toArray();

        $aggregate = new PostAggregate($posts);
        $iterator = $aggregate->createIterator();

        dd($iterator->first());

        $results = [];

        foreach ($iterator as $post) {
            $results[] = $post;
        }

        return $results;
    }
}
