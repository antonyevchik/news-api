<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePostRequest;
use App\Http\Requests\FindPostByIdRequest;
use App\Http\Requests\IndexPostsRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Language;
use App\Models\Post;
use App\Models\PostTranslation;
use Illuminate\Http\Request;
use App\Http\Interfaces\PostsInterface;


class PostsController extends Controller implements PostsInterface
{
    public function index(IndexPostsRequest $request)
    {
        return PostResource::collection(
            PostTranslation::latest()
                ->paginate($request->validated('per_page', 10) > 50 ? 50 : $request->validated('per_page'))
        );
    }

    public function findById(FindPostByIdRequest $request, Post $post)
    {
        return PostResource::make(
            $post->translations()->whereHas(
                'language',
                fn($query) => $query->where('prefix', $request->validated('lang'))
            )
                ->first()
        );
    }

    public function store(CreatePostRequest $request)
    {
        $post = Post::createFromRequest($request->validated());

        return response()->json([
            'message' => 'Post created!',
            'post_id' => $post->id
        ], 201);
    }

    public function update(UpdatePostRequest $request, Post $post)
    {
        Post::updateFromRequest($request, $post);

        return response()->json(['message' => 'Post updated!'], 201);
    }

    public function destroy(Post $post)
    {
        $post->delete();

        return response()->json(['message' => 'Post deleted!'], 204);
    }
}
