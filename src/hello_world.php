<?php

$fiber = new Fiber(function(): void {
    $value = Fiber::suspend('Hello');

    echo "Value used to resume fiber: ", $value, "\n";
});

$value = $fiber->start();

echo "Value from fiber suspending: ", $value, "\n";

$fiber->resume('World!');
