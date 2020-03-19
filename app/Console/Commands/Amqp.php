<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class Amqp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'amqp:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'send mq msg';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $connection = new AMQPStreamConnection('127.0.0.1', 5672, 'admin', '123456');
        $channel = $connection->channel();

        $channel->queue_declare('tesk_queue', false, false, false);
        $callback = function ($msg){
            echo "[x] Received ", $msg->body, "\n";
            sleep(substr_count($msg->body, '.'));
            echo "[x] Done", "\n";
            $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
        };
        $channel->basic_qos(null, 1, null);
        $channel->basic_consume('task_queue', '', false, false, false, false, $callback);

        while (count($channel->callbacks)) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();
    }
}
