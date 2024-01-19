<?php

namespace App\Tests\Stub;

use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Jwt\StaticTokenProvider;
use Symfony\Component\Mercure\Jwt\TokenFactoryInterface;
use Symfony\Component\Mercure\Jwt\TokenProviderInterface;
use Symfony\Component\Mercure\Update;

class HubStub implements HubInterface
{
    public function publish(Update $update): string
    {
        return 'urn:uuid:mock';
    }

    /**
     * Returns the Hub internal URL.
     */
    public function getUrl(): string
    {
        return 'http://localhost';
    }

    /**
     * Returns the Hub public URL.
     *
     * If the public URL is not configured, this method MUST return
     * the internal URL {@see HubInterface::getUrl()}.
     */
    public function getPublicUrl(): string
    {
        return 'https://example.com';
    }

    /**
     * Return the token provider used by this Hub.
     */
    public function getProvider(): TokenProviderInterface
    {
        return new StaticTokenProvider('foo');
    }

    /**
     * Return the token factory associated with this Hub.
     */
    public function getFactory(): ?TokenFactoryInterface
    {
        return new class() implements TokenFactoryInterface {
            public function create(?array $subscribe = [], ?array $publish = [], array $additionalClaims = []): string
            {
                return 'foo';
            }
        };
    }
}
