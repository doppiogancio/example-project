<?php

namespace DoppioGancio\ExampleProject\Controller;

use DoppioGancio\ExampleProject\Entity\User;
use DoppioGancio\ExampleProject\Repository\UserRepository;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Psr7\Response;

class UserController
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    public function indexAction(int $userId): Response
    {
        try {
            /** @var User $user */
            $user = $this->userRepository->getUserByIdAsync($userId)->wait();
        } catch (ClientException $exception) {
            return new Response(400, [], $exception->getMessage());
        } catch (ServerException $exception) {
            return new Response(500, [], 'Sorry there was a problem with our servers');
        }

        return new Response(
            200,
            [],
            sprintf(
                'Hi %s. You have %d posts, and %d albums',
                $user->name,
                count($user->posts),
                count($user->albums)
            )
        );
    }
}