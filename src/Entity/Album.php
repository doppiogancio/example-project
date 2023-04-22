<?php

namespace DoppioGancio\ExampleProject\Entity;

class Album
{
    public function __construct(
        public readonly int $userId,
        public readonly int $id,
        public readonly string $title,

    ) {
    }
}