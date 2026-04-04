<?php
require __DIR__ . '/vendor/autoload.php';


use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;


class ChatServer implements MessageComponentInterface {
    /** @var \SplObjectStorage */
    protected $clients;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn) {
        // Simpan koneksi
        $this->clients->attach($conn);

        // Ambil query string, misal ws://localhost:8080/chat?id_user=123
        $query = $conn->httpRequest->getUri()->getQuery();
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

    // Ambil data WAJIB (semua pakai id_user)
        $id_pengirim = $data['id_pengirim'];
        $id_penerima = $data['id_penerima'];
        $text = $data['text'];
    // Validasi sederhana
    if (!$id_pengirim || !$id_penerima || $text === '') {
        return;
    }

    // Koneksi DB
    $db = new mysqli("localhost", "root", "muhammadilham2372005", "chat_app");
    if ($db->connect_error) {
        return;
    }

    // Simpan pesan
    $stmt = $db->prepare("INSERT INTO pesan (id_Pengirim, id_Penerima, Pesan) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $id_pengirim, $id_penerima, $text);
    $stmt->execute();
    $stmt->close();

    // Payload ke client
    //untuk menyusun data supaya lebih rapi
    $payload = [
        'id_pengirim' => $id_pengirim,
        'id_penerima' => $id_penerima,
        'text' => htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
        'time' => date('H:i:s')
    ];

    // Broadcast ke pengirim & penerima
    // yang bagian ini yang membuatnya membagi secara real-time 
    foreach ($this->clients as $client) {
        $clientId = $client->id_user ?? null;

        if (!$clientId) continue;

        if ($clientId === $id_pengirim || $clientId === $id_penerima) {
            $client->send(json_encode($payload));
        }
    }

    // $db->close();
}

    public function onClose(ConnectionInterface $conn) {//function ini dipanggil ketika koneksi terputus oleh apapun 
        $this->clients->detach($conn);
        echo "Connection {$conn->resourceId} closed\n";//ini memunculkan pesan dilog php
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "Error: {$e->getMessage()}\n";
        $conn->close();
    }
}

$server = IoServer::factory(
    new HttpServer(
        new WsServer(new ChatServer())
    ),
    8081,
    '0.0.0.0' // penting
);

echo "WebSocket server running on ws://0.0.0.0:8081\n";

$server->run();