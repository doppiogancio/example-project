<?php

namespace DoppioGancio\ExampleProject\Repository;

use DoppioGancio\ExampleProject\Entity\User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use JMS\Serializer\Serializer;

class UserRepository
{
    public function __construct(
        private readonly Client $client,
        private readonly Serializer $serializer,
        private readonly PostRepository $postRepository,
        private readonly AlbumRepository $albumRepository,
    ) {
    }

    /**
     * @param int $id
     * @return PromiseInterface<User>
     */
    public function getUserByIdAsync(int $id): PromiseInterface
    {
        $response = $this->client->getAsync('/users/' . $id);

        return $response->then(function (Response $response) use ($id): User {
            $request = new Request('GET', '/users/' . $id);
            if ($response->getStatusCode() >= 500) {
                throw new ServerException("", $request, $response);
            }

            if ($response->getStatusCode() >= 400) {
                throw new ClientException("User not found!", $request, $response);
            }

            return $this->serializer->deserialize(
                $response->getBody()->getContents(),
                User::class,
                'json'
            );
        })->then(function (User $user) {
            $postsPromise = $this->postRepository->getUserPostsAsync($user->id);
            $albumsPromise = $this->albumRepository->getUserAlbumsAsync($user->id);
            $user->posts = $postsPromise->wait();
            $user->albums = $albumsPromise->wait();
            return $user;
        });
    }
}