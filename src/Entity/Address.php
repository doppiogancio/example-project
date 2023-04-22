<?php

namespace DoppioGancio\ExampleProject\Entity;

class Address
{
    public function __construct(
        public readonly string $street,
        public readonly string $suite,
        public readonly string $city,
        public readonly string $zipcode,
    ) {
    }
}