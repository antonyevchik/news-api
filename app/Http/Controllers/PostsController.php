<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePostRequest;
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

    public function findById(Request $request, Post $post)
    {
        return PostResource::make(
            $post->translations()->whereHas(
                'language',
                fn($query) => $query->where('prefix', $request->lang)
            )
                ->first()
        );
    }

    public function store(CreatePostRequest $request)
    {
        $post = Post::create();
        $post->translations()->create([
            'title' => $request->title,
            'description' => $request->description,
            'content' => $request->content,
            'language_id' => Language::where('prefix', $request->lang)->first()?->id || Language::create(['prefix' => $request->lang])->first()->id,
        ]);

        return response()->json(['message' => 'Post created!'], 201);
    }

    public function update(UpdatePostRequest $request, Post $post)
    {
        $post->translations()
            ->whereHas(
                'language',
                fn($query) => $query->where('prefix', $request->lang)
            )
            ->update([
                'title' => $request->title,
                'description' => $request->description,
                'content' => $request->content,
            ]);

        return response()->json(['message' => 'Post updated!'], 201);
    }

    public function destroy(Post $post)
    {
        $post->delete();

        return response()->json(['message' => 'Post deleted!'], 204);
    }
}
