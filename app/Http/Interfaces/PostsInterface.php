<?php

namespace App\Http\Interfaces;

use App\Http\Requests\CreatePostRequest;
use App\Http\Requests\FindPostByIdRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Post;

interface PostsInterface
{
    public function findById(FindPostByIdRequest $request, Post $post);
    public function store(CreatePostRequest $request);
    public function update(UpdatePostRequest $request, Post $post);
    public function destroy(Post $post);
}
