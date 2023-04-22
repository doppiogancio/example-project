<?php

namespace DoppioGancio\ExampleProject\Tests;

class FixtureLoader
{
    static public function load(string $filename): string
    {
        return file_get_contents(__DIR__ . '/fixtures/' . $filename);
    }
}