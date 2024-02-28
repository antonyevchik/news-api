<?php

namespace App\Http\Interfaces;

interface PostIteratorInterface
{
    public function first();
    public function current();
    public function next();
    public function isDone();
}
