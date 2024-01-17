<?php

require __DIR__ . '/../vendor/autoload.php';

use React\Http\Message\Response; 
use function React\Async\await;
use function React\Promise\Timer\sleep;

$http = new React\Http\HttpServer(function (Psr\Http\Message\ServerRequestInterface $request) {
    if ($request->getMethod() === 'GET') {
        return Response::plaintext(
            "Immediate response!\n"
        );
    } else {
        await(sleep(2));

        return React\Http\Message\Response::plaintext(
            "Not immediate, but that's OK :D!\n"
        );
    }
});

$socket = new React\Socket\SocketServer(isset($argv[1]) ? $argv[1] : '0.0.0.0:9090');
$http->listen($socket);

echo 'Listening on ' . str_replace('tcp:', 'http:', $socket->getAddress()) . PHP_EOL;
