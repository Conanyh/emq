<?php

namespace App\Console\Commands;

use GatewayWorker\BusinessWorker;
use GatewayWorker\Gateway;
use GatewayWorker\Register;
use Illuminate\Console\Command;
use Workerman\Worker;

class WorkermanCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'workman {action} {--d}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start a Workerman server';

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
        global $argv;
        $action = $this->argument('action');

        $argv[0] = 'wk';
        $argv[1] = $action;
        $argv[2] = $this->option('d') ? '-d' : '';

        $this->start();
    }

    private function start()
    {
        $this->startGateWay();
        $this->startBusinessWorker();
        $this->startRegister();
        Worker::runAll();
    }

    private function startBusinessWorker()
    {
        $worker = new BusinessWorker();
        $worker->name = 'BusinessWorker';
        $worker->count = 1;
        $worker->registerAddress = '127.0.0.1:1236';
        $worker->eventHandler = \App\Workerman\Events::class;
    }

    private function startGateWay()
    {
        // wss形式配置
        $context = array(
            'ssl' => array(
                // 请使用绝对路劲
                'local_cert' => '磁盘路径/ssl/certificate.pem', // 也可以是crt文件
                'local_pk' => '磁盘路径/ssl/privateKey.pem',
                'verify_peer' => false,
                // 'allow_self_signed' => true, //如果是自签名证书需要开启此选项
            )
        );

        $gateway = new Gateway("websocket://0.0.0.0:8890", $context);
        $gateway->transport = 'ssl';

        // ws形式
        $gateway = new Gateway("websocket://0.0.0.0:8890");
        $gateway->name = 'Gateway';
        $gateway->count = 1;
        $gateway->lanIp = '127.0.0.1';
        $gateway->startPort = 2300;
        $gateway->pingInterval = 30;
        $gateway->pingNotResponseLimit = 0;
        $gateway->pingData = '{"type":"@heart@"}';
        $gateway->registerAddress = '127.0.0.1:1236';
    }

    private function startRegister()
    {
        new Register('text://0.0.0.0:1236');
    }
}
