<?php

namespace App\Http\Interfaces;

use App\Http\Requests\CreatePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Post;
use Illuminate\Http\Request;

interface PostsInterface
{
    public function findById(Request $request, Post $post);
    public function store(CreatePostRequest $request);
    public function update(UpdatePostRequest $request, Post $post);
    public function destroy(Post $post);
}