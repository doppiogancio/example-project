<?php

namespace DoppioGancio\ExampleProject\Entity;

class User
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $username,
        public readonly string $email,
        public readonly string $phone,
        public readonly string $website,
        public readonly Address $address,
        public readonly Company $company,
        public array $posts = [],
        public array $albums = [],
    ) {
    }
}