<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class PostTranslation extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}
