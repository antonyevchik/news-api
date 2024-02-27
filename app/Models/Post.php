<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function translations()
    {
        return $this->hasMany(PostTranslation::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'post_tags');
    }

    public static function createFromRequest($request)
    {
        $post = isset($request['post_id']) ? Post::findOrFail($request['post_id'])->first() : Post::create();
        $post->translations()->create([
            'title' => $request['title'],
            'description' => $request['description'],
            'content' => $request['content'],
            'language_id' => Language::where('prefix', $request['lang'])->first()?->id || Language::create(['prefix' => $request['lang']])->first()->id,
        ]);

        if (isset($request['tags'])) {
            $tags = collect($request['tags'])
                ->map(
                    fn($tag) =>
                    Tag::firstOrCreate(['name' => $tag])->id
                );

            $post->tags()->sync($tags);
        }

        return $post;
    }

    public static function updateFromRequest($request, $post)
    {
        $post->translations()
            ->whereHas(
                'language',
                fn($query) => $query->where('prefix', $request['lang'])
            )
            ->update([
                'title' => $request['title'],
                'description' => $request['description'],
                'content' => $request['content'],
            ]);

        if (isset($request['tags'])) {
            $tags = collect($request['tags'])
                ->map(
                    fn($tag) =>
                    Tag::firstOrCreate(['name' => $tag])->id
                );

            $post->tags()->sync($tags);
        }

        return $post;
    }
}
