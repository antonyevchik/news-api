<?php

namespace App\Http\Resources;

use App\Models\PostTranslation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->post_id,
            'title' => $this->title,
            'description' => $this->description,
            'content' => $this->content,
            'tags' => TagResource::collection($this->post->tags),
        ];
    }
}
