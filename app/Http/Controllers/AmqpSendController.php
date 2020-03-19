<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class AmqpSendController extends Controller
{
    public function sendTask()
    {
        $connection = new AMQPStreamConnection('127.0.0.1', 5672, 'admin', '123456');
        $channel = $connection->channel();

        $channel->queue_declare('task_queue', false, false, false);
        if (empty($data)) $data = 'Hello World!--task';
        $msg = new AMQPMessage($data, [
            'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT
        ]);

        $channel->basic_publish($msg, '', 'task_queue');

        echo "[x] Send", $data, "\n";
        $channel->close();
        $connection->close();
    }
}
