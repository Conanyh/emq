<?php

namespace App\GatewayWorker;

use GatewayWorker\Lib\Gateway;
use Illuminate\Support\Facades\Log;

class Events
{
    public static function onWorkerStart($businessWorker)
    {
        echo "BusinessWorker    Start\n";
    }

    public static function onConnect($client_id)
    {
        Gateway::sendToClient('1', json_encode(['type' => 'init', 'client_id' => '1']));
    }

    public static function onWebSocketConnect($client_id, $data)
    {

    }

    public static function onMessage($client_id, $message)
    {
        $response = ['errcode' => 0, 'msg' => 'ok', 'data' => []];

        Gateway::sendToClient('1', json_encode($response));
    }

    public static function onClose($client_id)
    {
        Log::info('close connection' . 1);
    }

    private static function authentication($order_id, $user_id): bool
    {
        return 'authentication';   #判断属不属于这个订单的两个人
    }
}
