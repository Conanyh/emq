<?php
namespace Swoole;

// Swoole4�Ժ�ͨ��Э����ʵ���첽ͨ��
go(function () {
    $client = new Coroutine\Client(SWOOLE_SOCK_TCP);
    // ������ָ�� TCP ����˽������ӣ�IP�Ͷ˿ں���Ҫ�����˱���һ�£���ʱʱ��Ϊ0.5�룩
    if ($client->connect("127.0.0.1", 9503, 0.5)) {
        // �������Ӻ�������
        $client->send("hello world\n");
        // ��ӡ���յ�����Ϣ
        echo $client->recv();
        // �ر�����
        $client->close();
    } else {
        echo "connect failed.";
    }
});