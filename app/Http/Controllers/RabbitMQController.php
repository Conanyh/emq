<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQController extends Controller
{
    private function connection()
    {
        // create a connection
        $connection = new AMQPStreamConnection('localhost', 5672,'admin', '123456');
        $channel = $connection->channel();
    }

    private function close()
    {
        $channel->close();
        $connection->close();
    }


    public function sending()
    {
        $this->connection();

        // send, publish a message to queue
        $channel->queue_declare('hello', false, false, false, false);

        $msg = new AMQPMessage('Hello World!');
        $channel->basic_publish($msg, '', 'hello');

        echo "[x] Send 'Hello World' \n";

        $this->close();
    }

    public function receiving()
    {
        $this->connection();

        $channel->queue_declare('hello', false, false, false, false);

        echo "[*] Waiting for message. To exit press CTRL+C\n";

        $callback = function ($msg) {
            echo '[x] Received ', $msg->body, "\n";
        };

        $channel->basic_consume('hello', '', false, false, false, false, $callback);

        while($channel->is_consuming()) {
            $channel->wait();
        }

        $this->close();
    }


}
