<?php

namespace App\Services;

use App\Http\Interfaces\PostAggregateInterface;
use App\Http\Interfaces\PostIteratorInterface;

class PostAggregate implements PostAggregateInterface
{
    protected array $posts;

    public function __construct(array $posts)
    {
        $this->posts = $posts;
    }

    public function createIterator(): PostIteratorInterface
    {
        return new PostIterator($this->posts);
    }
}
