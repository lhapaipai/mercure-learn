<?php

namespace App\Service;

use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

class MyPublisher
{
    public function __construct(private HubInterface $hub)
    {
    }

    public function publish($topic, mixed $data): string
    {
        $update = new Update(
            $topic,
            json_encode($data),
            true
        );

        $id = $this->hub->publish($update);

        return $id;
    }
}
