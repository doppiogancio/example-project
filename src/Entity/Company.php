<?php

namespace DoppioGancio\ExampleProject\Entity;

class Company
{
    public function __construct(
        public readonly string $name,
        public readonly string $catchPhrase,
        public readonly string $bs,
    ) {
    }
}