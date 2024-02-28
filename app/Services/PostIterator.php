<?php

namespace App\Services;

use App\Http\Interfaces\PostIteratorInterface;

class PostIterator implements PostIteratorInterface
{
    protected array $posts;
    protected int $position = 0;

    public function __construct(array $posts)
    {
        $this->posts = $posts;
    }

    public function first()
    {
        return $this->posts[0];
    }
    public function current()
    {
        return $this->posts[$this->position];
    }

    public function next()
    {
        ++$this->position;
    }

    public function isDone(): bool
    {
        return isset($this->posts[$this->position]);
    }
}
