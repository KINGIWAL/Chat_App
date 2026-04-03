<?php
require __DIR__ . '/vendor/autoload.php';

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\App;

class ChatServer implements MessageComponentInterface {
    protected $clients;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        echo "New connection: {$conn->resourceId}\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        echo "Message received: $msg\n";

        // Broadcast ke semua client
        foreach ($this->clients as $client) {
            $client->send($msg);
        }
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $data = json_decode($msg, true);

        // Simpan ke database
        $conn = new mysqli("localhost", "root", "muhammadilham2372005", "chat_app");
        $stmt = $conn->prepare("INSERT INTO pesan (id_Pengirim, id_Penerima, Pesan) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $data['id_pengirim'], $data['id_penerima'], $data['text']);
        $stmt->execute();
        $stmt->close();
        $conn->close();

        // Broadcast ke semua client
        foreach ($this->clients as $client) {
            $client->send($msg);
        }
    }

    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
        echo "Connection {$conn->resourceId} closed\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "Error: {$e->getMessage()}\n";
        $conn->close();
    }
}

$app = new App('localhost', 8080);
$app->route('/chat', new ChatServer, ['*']);
$app->run();
