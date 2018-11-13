<?php
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use ChatServer\Models\SimpleChat;

require_once dirname(__DIR__) . '/vendor/autoload.php';

// Start connection
$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new SimpleChat()
        )
    ),
    8080
);

$server->run();