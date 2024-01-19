<?php

namespace App;

class SSE
{
    protected $eventBuilder;

    public function __construct(EventBuilder $eventBuilder)
    {
        $this->eventBuilder = $eventBuilder;
    }

    /**
     * Start SSE Server.
     *
     * @param int $interval in seconds
     */
    public function start($interval = 1)
    {
        while (true) {
            try {
                echo $this->eventBuilder->getEvent();
            } catch (\Exception $e) {
                return;
            }

            if (ob_get_level() > 0) {
                ob_flush();
            }

            flush();

            // if the connection has been closed by the client we better exit the loop
            if (connection_aborted()) {
                return;
            }
            sleep($interval);
        }
    }
}
