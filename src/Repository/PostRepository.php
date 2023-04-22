<?php

namespace DoppioGancio\ExampleProject\Repository;

use DoppioGancio\ExampleProject\Entity\Post;
use GuzzleHttp\Client;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Response;
use JMS\Serializer\Serializer;

class PostRepository
{
    public function __construct(
        private readonly Client $client,
        private readonly Serializer $serializer,
    ) {
    }

    public function getUserPostsAsync(int $userId): PromiseInterface
    {
        $response = $this->client->getAsync('/posts/');

        return $response->then(function (Response $response) use ($userId) {
            $posts = $this->serializer->deserialize(
                $response->getBody()->getContents(),
                sprintf('array<%s>', Post::class),
                'json'
            );

            return array_filter($posts, static function (Post $post) use ($userId) {
                return $post->userId == $userId;
            });
        });
    }
}