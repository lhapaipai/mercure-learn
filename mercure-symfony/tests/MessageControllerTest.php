<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Mercure\Jwt\StaticTokenProvider;
use Symfony\Component\Mercure\MockHub;
use Symfony\Component\Mercure\Update;

class MessageControllerTest extends TestCase
{
    public function testSomething(): void
    {
        $hub = new MockHub('https://internal/.well-known/mercure', new StaticTokenProvider('foo'), function (Update $update): string {
            $this->assertTrue($update->isPrivate());

            return 'id';
        });

        $update = new Update(
            'http://localhost/my-private-topic',
            json_encode([
                'status' => 'OutOfStock',
            ]),
            true
        );

        $hub->publish($update);
    }
}
