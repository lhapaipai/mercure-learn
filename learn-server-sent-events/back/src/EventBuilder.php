<?php

namespace App;

class EventBuilder
{
    /**
     * @var string the event ID to set the EventSource object's last event ID value
     */
    protected $id;

    /**
     * @var string A string identifying the type of event described. If this is specified, an event will be dispatched on the browser to the listener for the specified event name; the website source code should use addEventListener() to listen for named events. The onmessage handler is called if no event name is specified for a message.
     */
    protected $event;

    /**
     * @var string The initial
     */
    protected $initialEvent;

    /**
     * @var string The data field for the message. When the EventSource receives multiple consecutive lines that begin with data:, it will concatenate them, inserting a newline character between each one. Trailing newlines are removed.
     */
    protected $data;

    /**
     * @var int The reconnection time to use when attempting to send the event. This must be an integer, specifying the reconnection time in milliseconds. If a non-integer value is specified, the field is ignored.
     */
    protected $retry;

    /**
     * @var string This is just a comment, since it starts with a colon character. As mentioned previously, this can be useful as a keep-alive if messages may not be sent regularly.
     */
    protected $comment;

    /**
     * @var callable The callback to get event data. Throw a {@see StopSSEException} to stop the execution, browser will retry after {@see}
     */
    protected $callback;

    /**
     * Event constructor.
     *
     * @param callable $callback {@see Event::$callback}
     * @param string   $event    {@see Event::$event}
     * @param int      $retry    {@see Event::$retry}
     */
    public function __construct(callable $callback, $event = '', $retry = 5000)
    {
        $this->callback = $callback;
        $this->id = '';
        $this->data = '';
        $this->initialEvent = $this->event = $event;
        $this->retry = $retry;
        $this->comment = '';
    }

    /**
     * get the event data & id.
     *
     * @return $this
     *
     * @throws Exception
     */
    public function getEvent()
    {
        $this->event = $this->initialEvent;
        $result = call_user_func($this->callback);
        if (false === $result) {
            $this->id = '';
            $this->data = '';
            $this->comment = 'no data';
        } else {
            if (isset($result['event'])) {
                $this->event = $result['event'];
            }
            $this->id = isset($result['id']) ? $result['id'] : str_replace('.', '', uniqid('', true));
            $this->data = isset($result['data']) ? $result['data'] : $result;
            $this->comment = isset($result['comment']) ? $result['comment'] : '';
        }

        return $this;
    }

    public function __toString()
    {
        $event = [];
        if ('' !== $this->comment) {
            $event[] = sprintf(': %s', $this->comment);
        }
        if ('' !== $this->id) {
            $event[] = sprintf('id: %s', $this->id);
        }
        if ($this->retry > 0) {
            $event[] = sprintf('retry: %s', $this->retry);
        }
        if ('' !== $this->event) {
            $event[] = sprintf('event: %s', $this->event);
        }
        if ('' !== $this->data) {
            $event[] = sprintf('data: %s', $this->data);
        }

        return implode("\n", $event)."\n\n";
    }
}
