<?php

require_once __DIR__.'/src/EventBuilder.php';
require_once __DIR__.'/src/SSE.php';

use App\EventBuilder;
use App\SSE;

header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');
header('X-Accel-Buffering: no'); // Nginx: unbuffered responses suitable for Comet and HTTP streaming applications

$callback = function () {
    $id = mt_rand(1, 100000000);

    $event = [
        'id' => $id,
        'event' => 'news',
        'data' => json_encode([
            'title' => 'title '.$id,
            'content' => 'content '.$id,
        ]),
        'comment' => 'hello world',
    ];

    $shouldStop = 1 === rand(1, 10); // Stop if something happens or to clear connection, browser will retry
    if ($shouldStop) {
        throw new Exception();
    }

    return $event;
};
(new SSE(new EventBuilder($callback)))->start();
