<?php

namespace Oxygen\Core\WebSocket;

/**
 * OxygenWebSocket - Built-in WebSocket Server
 * 
 * Real-time features without Laravel Echo, Pusher, or Redis.
 * Simple, fast, and built-in.
 * 
 * @package    Oxygen\Core\WebSocket
 * @author     Redwan Aouni <aouniradouan@gmail.com>
 * @copyright  2024 - OxygenFramework
 * @version    2.0.0
 */
class OxygenWebSocket
{
    protected $clients = [];
    protected $socket = null;
    protected $port = 8080;
    protected $channels = [];

    /**
     * Start WebSocket server
     */
    public function start($port = 8080)
    {
        $this->port = $port;

        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        socket_set_option($this->socket, SOL_SOCKET, SO_REUSEADDR, 1);
        socket_bind($this->socket, '0.0.0.0', $this->port);
        socket_listen($this->socket);

        echo "🚀 OxygenWebSocket server started on port {$this->port}\n";

        while (true) {
            $read = array_merge([$this->socket], $this->clients);
            socket_select($read, $write, $except, null);

            if (in_array($this->socket, $read)) {
                $client = socket_accept($this->socket);
                $this->clients[] = $client;
                $this->handshake($client);

                echo "✅ New client connected\n";
            }

            foreach ($this->clients as $key => $client) {
                if (in_array($client, $read)) {
                    $data = @socket_read($client, 2048);

                    if ($data === false || $data === '') {
                        unset($this->clients[$key]);
                        socket_close($client);
                        echo "❌ Client disconnected\n";
                        continue;
                    }

                    $message = $this->decode($data);
                    $this->handleMessage($client, $message);
                }
            }
        }
    }

    /**
     * WebSocket handshake
     */
    protected function handshake($client)
    {
        $request = socket_read($client, 5000);

        preg_match('/Sec-WebSocket-Key: (.*)\r\n/', $request, $matches);
        $key = $matches[1] ?? '';

        $acceptKey = base64_encode(sha1($key . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11', true));

        $response = "HTTP/1.1 101 Switching Protocols\r\n";
        $response .= "Upgrade: websocket\r\n";
        $response .= "Connection: Upgrade\r\n";
        $response .= "Sec-WebSocket-Accept: $acceptKey\r\n\r\n";

        socket_write($client, $response);
    }

    /**
     * Decode WebSocket frame
     */
    protected function decode($data)
    {
        $length = ord($data[1]) & 127;

        if ($length == 126) {
            $masks = substr($data, 4, 4);
            $payload = substr($data, 8);
        } elseif ($length == 127) {
            $masks = substr($data, 10, 4);
            $payload = substr($data, 14);
        } else {
            $masks = substr($data, 2, 4);
            $payload = substr($data, 6);
        }

        $text = '';
        for ($i = 0; $i < strlen($payload); $i++) {
            $text .= $payload[$i] ^ $masks[$i % 4];
        }

        return json_decode($text, true);
    }

    /**
     * Encode WebSocket frame
     */
    protected function encode($message)
    {
        $data = json_encode($message);
        $frame = chr(129);
        $length = strlen($data);

        if ($length <= 125) {
            $frame .= chr($length);
        } elseif ($length <= 65535) {
            $frame .= chr(126) . pack('n', $length);
        } else {
            $frame .= chr(127) . pack('J', $length);
        }

        return $frame . $data;
    }

    /**
     * Handle incoming message
     */
    protected function handleMessage($client, $message)
    {
        if (!$message)
            return;

        $action = $message['action'] ?? '';

        switch ($action) {
            case 'subscribe':
                $this->subscribe($client, $message['channel'] ?? '');
                break;
            case 'message':
                $this->broadcast($message['channel'] ?? '', $message['data'] ?? []);
                break;
        }
    }

    /**
     * Subscribe to channel
     */
    protected function subscribe($client, $channel)
    {
        if (!isset($this->channels[$channel])) {
            $this->channels[$channel] = [];
        }

        $this->channels[$channel][] = $client;
        echo "📢 Client subscribed to channel: $channel\n";
    }

    /**
     * Broadcast message to channel
     */
    public function broadcast($channel, $data)
    {
        if (!isset($this->channels[$channel])) {
            return;
        }

        $message = $this->encode([
            'channel' => $channel,
            'data' => $data
        ]);

        foreach ($this->channels[$channel] as $client) {
            @socket_write($client, $message);
        }
    }

    /**
     * Send to specific client
     */
    public function send($client, $data)
    {
        $message = $this->encode($data);
        socket_write($client, $message);
    }
}
