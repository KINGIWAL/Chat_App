<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/db.php'; // file ini harus mendefinisikan $pdo (PDO instance)

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;

class ChatServer implements MessageComponentInterface {
    /** @var \SplObjectStorage */
    protected $clients;
    /** @var \PDO */
    protected $pdo;

    public function __construct(\PDO $pdo) {
        $this->clients = new \SplObjectStorage;
        $this->pdo = $pdo;
    }

    public function onOpen(ConnectionInterface $conn) {
        // Simpan koneksi
        $this->clients->attach($conn);

        // Ambil query string, misal ws://localhost:8081/?id_user=123
        // Pastikan client mengirim id_user di query string saat membuka koneksi
        $query = '';
        if (isset($conn->httpRequest)) {
            $query = $conn->httpRequest->getUri()->getQuery();
        } elseif (method_exists($conn, 'WebSocket')) {
            // fallback jika implementasi berbeda
            $query = '';
        }
        parse_str($query, $params);

        // Simpan id_user (internal DB id) ke objek koneksi agar bisa difilter nanti
        $conn->id_user = isset($params['id_user']) ? (int)$params['id_user'] : null;

        echo "New connection: {$conn->resourceId} (user_id: {$conn->id_user})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        echo "Message from {$from->resourceId}: {$msg}\n";

        // Decode JSON
        $data = json_decode($msg, true);
        if (!is_array($data)) {
            return;
        }

        $id_pengirim = isset($data['id_pengirim']) ? (int)$data['id_pengirim'] : null;
        $id_penerima = isset($data['id_penerima']) ? (int)$data['id_penerima'] : null;
        $text = isset($data['text']) ? (string)$data['text'] : '';

        // Validasi sederhana
        if (!$id_pengirim || !$id_penerima || $text === '') {
            return;
        }

        // Simpan pesan ke database dengan PDO
        try {
            $stmt = $this->pdo->prepare(
                "INSERT INTO pesan (id_Pengirim, id_Penerima, Pesan, time_Pengiriman)
                 VALUES (?, ?, ?, NOW())"
            );
            $stmt->execute([$id_pengirim, $id_penerima, $text]);
        } catch (\PDOException $e) {
            error_log("Failed to insert message: " . $e->getMessage());
            return;
        }

        // Susun payload yang akan dikirim ke client
        $payload = [
            'id_pengirim' => $id_pengirim,
            'id_penerima' => $id_penerima,
            'text' => htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
            'time' => date('H:i:s')
        ];

        // Broadcast ke pengirim & penerima
        foreach ($this->clients as $client) {
            $clientId = $client->id_user ?? null;
            if (!$clientId) continue;

            if ($clientId === $id_pengirim || $clientId === $id_penerima) {
                $client->send(json_encode($payload));
            }
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

// Pastikan $pdo sudah tersedia dari require db.php
if (!isset($pdo) || !($pdo instanceof \PDO)) {
    echo "PDO connection not found. Check db.php\n";
    exit(1);
}

$chatServer = new ChatServer($pdo);

$server = IoServer::factory(
    new HttpServer(
        new WsServer($chatServer)
    ),
    8081,
    '0.0.0.0'
);

echo "WebSocket server running on ws://0.0.0.0:8081\n";
$server->run();
