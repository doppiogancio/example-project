<?php

use DoppioGancio\ExampleProject\Entity\User;
use DoppioGancio\ExampleProject\Repository\PostRepository;
use DoppioGancio\ExampleProject\Repository\UserRepository;
use GuzzleHttp\Client;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\Naming\SerializedNameAnnotationStrategy;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;

require_once 'vendor/autoload.php';

$client = new Client([
    'base_uri' => 'https://jsonplaceholder.typicode.com',
]);
$serializer = SerializerBuilder::create()
    ->setPropertyNamingStrategy(new SerializedNameAnnotationStrategy(
        new IdenticalPropertyNamingStrategy()
    ))
    ->build();

$container = new DI\Container();
$container->set(Client::class, $client);
$container->set(Serializer::class, $serializer);

$postRepository = $container->get(PostRepository::class);
$userRepository = $container->get(UserRepository::class);

/** @var User $user */
$user = $userRepository->getUserByIdAsync(1)->wait();
print_r($user);
