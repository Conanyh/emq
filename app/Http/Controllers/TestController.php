<?php

namespace App\Http\Controllers;

use App\Jobs\TestQueue;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function store(Request $request)
    {
        echo 'this is queue';
        $data['title'] = $request->get('title', 'this is test queue');
        $data['time'] = date('Y-m-d H:i:s');
        $job = (new TestQueue($data))->onQueue('testqueue');
        $this->dispatch($job);
    }

    public function push()
    {
        $fd = 1;
        $swoole = app('swoole');
        $success = $swoole->push($fd, 'Push data to fd#1 in Controller');
        var_dump($success);
    }
}
