<?php

namespace DoppioGancio\ExampleProject\Tests\Controller;

use DI\Container;
use DoppioGancio\ExampleProject\Controller\UserController;
use DoppioGancio\ExampleProject\Tests\Client\MockedClientFactory;
use GuzzleHttp\Client;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\Naming\SerializedNameAnnotationStrategy;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use PHPUnit\Framework\TestCase;

class UserControllerTest extends TestCase
{
    static private Container $container;
    static private UserController $userController;

    public static function setUpBeforeClass(): void
    {
        self::$container = new Container();
        self::injectSerializer();
        self::injectMockedClient();

        self::$userController = self::$container->get(UserController::class);
    }

    public function testIndexAction(): void
    {
        $response = self::$userController->indexAction(1);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("Hi Leanne Graham. You have 10 posts, and 10 albums", $response->getBody()->getContents());
    }

    public function testClientError(): void
    {
        $response = self::$userController->indexAction(2);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals("User not found!", $response->getBody()->getContents());
    }

    public function testServerError(): void
    {
        $response = self::$userController->indexAction(3);

        $this->assertEquals(500, $response->getStatusCode());
    }

    static private function injectMockedClient(): void
    {
        self::$container->set(Client::class, MockedClientFactory::create());
    }

    static private function injectSerializer(): void
    {
        $serializer = SerializerBuilder::create()
            ->setPropertyNamingStrategy(new SerializedNameAnnotationStrategy(
                new IdenticalPropertyNamingStrategy()
            ))
            ->build();

        self::$container->set(Serializer::class, $serializer);
    }
}
