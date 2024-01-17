<?php

use Psr\Http\Message\ResponseInterface;
use React\Http\Browser;

require __DIR__ . '/../vendor/autoload.php';

$client = new Browser();

$time = time();

$client->get('http://localhost:9000/slow.php')->then(function (ResponseInterface $response) {
    var_dump((string)$response->getBody());
}, function (Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
});

$client->get('http://localhost:9000/fast.php')->then(function (ResponseInterface $response) {
    var_dump((string)$response->getBody());
}, function (Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
});
