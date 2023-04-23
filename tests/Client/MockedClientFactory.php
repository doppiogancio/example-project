<?php

namespace DoppioGancio\ExampleProject\Tests\Client;

use DoppioGancio\MockedClient\HandlerBuilder;
use DoppioGancio\MockedClient\MockedGuzzleClientBuilder;
use DoppioGancio\MockedClient\Route\RouteBuilder;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Http\Discovery\Psr17FactoryDiscovery;

class MockedClientFactory
{
    static public function create(): Client
    {
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
                ->withPath('/users/2')
                ->withResponse(new Response(400))
                ->build()
        );

        $handlerBuilder->addRoute(
            $route->new()
                ->withMethod('GET')
                ->withPath('/users/3')
                ->withResponse(new Response(500, [], 'Unable to connect to the database.'))
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
        return $clientBuilder->build();
    }
}