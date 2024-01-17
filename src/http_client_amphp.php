<?php

require __DIR__ . '/../vendor/autoload.php';

use Amp\CancelledException;
use Amp\Http\Client\HttpClientBuilder;
use Amp\Http\Client\Request;
use Amp\TimeoutCancellation;
use function Amp\async;

$httpClient = HttpClientBuilder::buildDefault();

$start = time();

try {
    $mainRequestFuture = async(fn () => $httpClient->request(
        new Request("http://localhost:9000/slow.php", 'GET')
    ));

    $resource = $mainRequestFuture->await(new TimeoutCancellation(2));
} catch (CancelledException $e) {
    $fallbackResource = async(fn () => $httpClient->request(
        new Request("http://localhost:9000/fast.php", 'GET')
    ));
    $resource = $fallbackResource->await();
}

var_dump($resource->getBody()->buffer());

echo "::: It took " . time() - $start . " seconds to fetch the resources" . PHP_EOL;
