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

        return $post;
    }
}
