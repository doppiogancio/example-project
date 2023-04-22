<?php

namespace DoppioGancio\ExampleProject\Repository;

use DoppioGancio\ExampleProject\Entity\Album;
use GuzzleHttp\Client;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Response;
use JMS\Serializer\Serializer;

class AlbumRepository
{
    public function __construct(
        private readonly Client $client,
        private readonly Serializer $serializer,
    ) {
    }

    public function getUserAlbumsAsync(int $userId): PromiseInterface
    {
        $response = $this->client->getAsync('/albums/');

        return $response->then(function (Response $response) use ($userId) {
            $posts = $this->serializer->deserialize(
                $response->getBody()->getContents(),
                sprintf('array<%s>', Album::class),
                'json'
            );

            return array_filter($posts, static function (Album $album) use ($userId) {
                return $album->userId == $userId;
            });
        });
    }
}