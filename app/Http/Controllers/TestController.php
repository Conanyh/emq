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
}
