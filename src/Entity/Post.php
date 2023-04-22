<?php

namespace DoppioGancio\ExampleProject\Entity;

class Post
{
    public function __construct(
        public readonly int $userId,
        public readonly int $id,
        public readonly string $title,
        public readonly string $body,
    ) {
    }
}