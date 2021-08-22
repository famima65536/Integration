<?php

namespace integration\websocket;

use Exception;
use Ratchet\ConnectionInterface;
use Ratchet\RFC6455\Messaging\MessageInterface;
use Ratchet\MessageComponentInterface;
use SplObjectStorage;


class InternalWebSocketServer implements MessageComponentInterface
{

    protected SplObjectStorage $clients;

    public function __construct()
    {
        $this->clients = new SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn): void
    {
        $this->clients->attach($conn);
    }

    public function onError(ConnectionInterface $conn, Exception $e):void
    {
        $conn->close();
    }

    public function onMessage(ConnectionInterface $from, $msg):void
    {
        foreach ($this->clients as $client) {
            if ($from !== $client) {
                $client->send($msg);
            }
        }
    }

    public function onClose(ConnectionInterface $conn):void
    {
        $this->clients->detach($conn);
    }
}