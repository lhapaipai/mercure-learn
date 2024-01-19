<?php

namespace App\Tests;

use App\Service\MyPublisher;
use App\Tests\Stub\HubStub;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Mercure\HubInterface;

class MyPublisherTest extends KernelTestCase
{
    public function testStubHub(): void
    {
        $kernel = self::bootKernel();

        static::getContainer()->set(HubInterface::class, new HubStub());
        $this->assertSame('test', $kernel->getEnvironment());

        /** @var MyPublisher $myPublisher */
        $myPublisher = static::getContainer()->get(MyPublisher::class);

        $id = $myPublisher->publish('http://localhost/my-private-topic', [
            'status' => 'OutOfStock',
        ]);

        static::assertSame($id, 'urn:uuid:mock');
    }
}
