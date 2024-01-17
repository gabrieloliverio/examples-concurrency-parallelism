<?php

use Psr\Http\Message\ResponseInterface;
use React\Http\Browser;

use function React\Promise\all;
use function React\Promise\race;

require __DIR__ . '/../vendor/autoload.php';

$client = new Browser();

$time = time();

$request1 = $client->get('http://localhost:9000/slow.php');
$request2 = $client->get('http://localhost:9000/fast.php');

$bodies = [];
all([$request1, $request2])->then(function (array $responses) {
    $bodies = array_map(fn($response) => (string) $response->getBody(), $responses);

    var_dump($bodies);
}, function (Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
});

race([$request1, $request2])->then(function (ResponseInterface $response) {
    var_dump((string)$response->getBody());
}, function (Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
});
