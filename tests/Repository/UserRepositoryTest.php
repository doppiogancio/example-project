<?php

namespace DoppioGancio\ExampleProject\Tests\Repository;

use DI\Container;
use DoppioGancio\ExampleProject\Entity\User;
use DoppioGancio\ExampleProject\Repository\UserRepository;
use DoppioGancio\ExampleProject\Tests\FixtureLoader;
use DoppioGancio\MockedClient\HandlerBuilder;
use DoppioGancio\MockedClient\MockedGuzzleClientBuilder;
use DoppioGancio\MockedClient\Route\RouteBuilder;
use GuzzleHttp\Client;
use GuzzleHttp\Promise\FulfilledPromise;
use GuzzleHttp\Psr7\Response;
use Http\Discovery\Psr17FactoryDiscovery;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\Naming\SerializedNameAnnotationStrategy;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use PHPUnit\Framework\TestCase;

class UserRepositoryTest extends TestCase
{
    static private Container $container;

    public function testCanGetUserFromPhpunitMockedClient(): void
    {
        $this->bootstrapContainerForPhpunitMockedClient();

        $repo = self::$container->get(UserRepository::class);

        /** @var User $user */
        $user = $repo->getUserByIdAsync(1)->wait();
        $this->assertUser($user);
    }

    public function testCanGetUserFromLivePlaceholder(): void
    {
        $this->bootstrapContainerForLivePlaceholder();

        $repo = self::$container->get(UserRepository::class);

        /** @var User $user */
        $user = $repo->getUserByIdAsync(1)->wait();
        $this->assertUser($user);
    }

    public function testCanGetUserFromDoppioGancioMockedClient(): void
    {
        $this->bootstrapContainerForDoppioGancioMockedClient();

        $repo = self::$container->get(UserRepository::class);

        /** @var User $user */
        $user = $repo->getUserByIdAsync(1)->wait();
        $this->assertUser($user);
    }

    private function bootstrapContainerForLivePlaceholder(): void
    {
        self::$container = new Container();

        $client = new Client([
            'base_uri' => 'https://jsonplaceholder.typicode.com',
        ]);

        self::$container->set(Client::class, $client);
        $this->injectSerializer();
    }

    private function bootstrapContainerForDoppioGancioMockedClient(): void
    {
        self::$container = new Container();

        $handlerBuilder = new HandlerBuilder(
            Psr17FactoryDiscovery::findServerRequestFactory()
        );

        $route = new RouteBuilder(
            Psr17FactoryDiscovery::findResponseFactory(),
            Psr17FactoryDiscovery::findStreamFactory(),
        );

        $handlerBuilder->addRoute(
            $route->new()
                ->withMethod('GET')
                ->withPath('/users/1')
                ->withFileResponse(__DIR__ . '/../fixtures/user_1.json')
                ->build()
        );

        $handlerBuilder->addRoute(
            $route->new()
                ->withMethod('GET')
                ->withPath('/posts/')
                ->withFileResponse(__DIR__ . '/../fixtures/posts.json')
                ->build()
        );

        $handlerBuilder->addRoute(
            $route->new()
                ->withMethod('GET')
                ->withPath('/albums/')
                ->withFileResponse(__DIR__ . '/../fixtures/albums.json')
                ->build()
        );

        $clientBuilder = new MockedGuzzleClientBuilder($handlerBuilder);

        self::$container->set(Client::class, $clientBuilder->build());
        $this->injectSerializer();
    }

    private function bootstrapContainerForPhpunitMockedClient(): void
    {
        self::$container = new Container();

        $client = $this->createMock(Client::class);
        $client->method('getAsync')
            ->willReturnOnConsecutiveCalls(
                new FulfilledPromise(new Response(200, [], FixtureLoader::load('user_1.json'))),
                new FulfilledPromise(new Response(200, [], FixtureLoader::load('posts.json'))),
                new FulfilledPromise(new Response(200, [], FixtureLoader::load('albums.json'))),
            );

        self::$container->set(Client::class, $client);
        $this->injectSerializer();
    }

    private function injectSerializer(): void
    {
        $serializer = SerializerBuilder::create()
            ->setPropertyNamingStrategy(new SerializedNameAnnotationStrategy(
                new IdenticalPropertyNamingStrategy()
            ))
            ->build();

        self::$container->set(Serializer::class, $serializer);
    }

    /**
     * @param User $user
     * @return void
     */
    private function assertUser(User $user): void
    {
        $this->assertEquals(1, $user->id);
        $this->assertEquals('Leanne Graham', $user->name);
        $this->assertEquals('Romaguera-Crona', $user->company->name);
        $this->assertCount(10, $user->posts);
        $this->assertCount(10, $user->albums);
    }
}