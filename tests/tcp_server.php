<?php
namespace Swoole;

// �������� 9503 �˿ڣ��ȴ��ͻ�������
$server = new Server("127.0.0.1", 9503);
// ��������ʱ���
$server->on('connect', function ($serv, $fd){
    echo "Client:Connect.\n";
});
// ������Ϣʱ��������
$server->on('receive', function ($serv, $fd, $from_id, $data) {
    $serv->send($fd, 'Swoole: '.$data);
    $serv->close($fd);
});
// ���ӹر�ʱ���
$server->on('close', function ($serv, $fd) {
    echo "Client: Close.\n";
});
// ���� TCP ������
$server->start();