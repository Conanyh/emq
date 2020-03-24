<?php
// 间隔时钟定时器
//$timerId = \Swoole\Timer::tick(1000, function () {
//    echo "Swoole 很棒\n";
//});

// 指定时钟定时器
$timerId = \Swoole\Timer::after(3000, function () {
    echo "Laravel 也很棒\n";
});

// 清除定时器  这种情况下，两个定时器都不会调用
//\Swoole\Timer::clear($timerId);

// 对于间隔时钟定时器，还可以这么清除
$count = 0;
\Swoole\Timer::tick(1000, function ($timerId, $count){
    global $count;
    echo "Swoole 很棒\n";
    $count++;
    if ($count == 3) {
        \Swoole\Timer::clear($timerId);
    }
}, $count);
