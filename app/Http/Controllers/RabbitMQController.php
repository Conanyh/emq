<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQController extends Controller
{
    /*
     * RabbitMQ Tutorials
     * https://www.rabbitmq.com/getstarted.html
     *
     * 消息发送和接收
     * Hello World
     * sending receiving
     */
    public function sending()
    {
        // create a connection
        $connection = new AMQPStreamConnection('localhost', 5672,'admin', '123456');
        $channel = $connection->channel();

        // send, publish a message to queue
        $channel->queue_declare('hello', false, false, false, false);

        $msg = new AMQPMessage('Hello World!');
        $channel->basic_publish($msg, '', 'hello');

        echo "[x] Send 'Hello World' \n";

        $channel->close();
        $connection->close();
    }

    public function receiving()
    {
        $connection = new AMQPStreamConnection('localhost', 5672,'admin', '123456');
        $channel = $connection->channel();

        $channel->queue_declare('hello', false, false, false, false);

        echo "[*] Waiting for message. To exit press CTRL+C\n";

        $callback = function ($msg) {
            echo '[x] Received ', $msg->body, "\n";
        };

        $channel->basic_consume('hello', '', false, false, false, false, $callback);

        while($channel->is_consuming()) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();
    }

    /*
     * 消息分发机制
     * Work queues
     * new_task worker
     */
    public function newTask()
    {
        // create a connection
        $connection = new AMQPStreamConnection('localhost', 5672,'admin', '123456');
        $channel = $connection->channel();

        $channel->queue_declare('task_queue', false, false, false, false);

        $data = implode(' ', array_slice($argv, 1));
        if (empty($data)) {
            $data = 'Hello World!';
        }
        $msg = new AMQPMessage(
            $data,
            array('delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT)
        );

        $channel->basic_publish($msg, '', 'task_queue');

        echo ' [x] Send', $data, "\n";

        $channel->close();
        $connection->close();
    }

    public function worker()
    {
        $connection = new AMQPStreamConnection('localhost', 5672,'admin', '123456');
        $channel = $connection->channel();

        $channel->queue_declare('task_queue', false, false, false, false);

        echo " [*] Waiting for messages. To exit press CTRL+C\n";

        $callback = function ($msg) {
            echo ' [x] Received' , $msg->body, "\n";
            sleep(substr_count($msg->body, '.'));
            echo ' [x] Done\n';
            $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
        };

        $channel->basic_qos(null, 1, null);
        $channel->basic_consume('task_queue', '', false, false, false, false, $callback);

        while($channel->is_consuming()) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();
    }


}
