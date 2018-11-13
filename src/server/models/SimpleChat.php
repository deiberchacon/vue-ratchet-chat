<?php
namespace ChatServer\Models;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class SimpleChat implements MessageComponentInterface {
    protected $clients;

    /**
     * Simple Chat constructor
     */
    public function __construct() {
        // Storage collection for connected client
        $this->clients = new \SplObjectStorage;
    }

    /**
     * Called when a client connects to a WebSockect
     *
     * @param ConnectionInterface $conn
     * @return void
     */
    public function onOpen(ConnectionInterface $conn) {
        // Add client to the collection
        $this->clients->attach($conn);
        echo "Connected client ({$conn->resourceId})" . PHP_EOL;
    }

    /**
     * Called when a client send data to a WebSocket
     *
     * @param ConnectionInterface $from
     * @param string $data
     * @return void
     */
    public function onMessage(ConnectionInterface $from, $data) {
        // Transform data received and add the date
        $data = json_decode($data);
        $data->date = date('d/m/Y H:i:s');

        // Loop through connected clients and send the message
        // for ech of them
        foreach ($this->clients as $client) {
            $client->send(json_encode($data));
        }

        echo "Client {$from->resourceId} send a message" . PHP_EOL;
    }

    /**
     * Called when the client disconnects from when WebSocket
     *
     * @param ConnectionInterface $conn
     * @return void
     */
    public function onClose(ConnectionInterface $conn) {
        // Remove client from the collection
        $this->clients->detach($conn);
        echo "Client {$conn->resourceId} desconnected" . PHP_EOL;
    }

    /**
     * Called when the WebSocket has an error
     *
     * @param ConnectionInterface $conn
     * @param \Exception $e
     * @return void
     */
    public function onError(ConnectionInterface $conn, \Exception $e) {
        // Close connection with client
        $conn->close();
        echo "Error: {$e->getMessage()}" . PHP_EOL;
    }
}