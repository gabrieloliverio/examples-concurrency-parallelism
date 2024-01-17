<?php

require __DIR__ . '/../vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Promise;

$client = new Client();

$url = 'http://localhost:9000/slow.php';

$start = time();

$promises['request1'] = $client->getAsync($url);
$promises['request2'] = $client->getAsync($url);

$responses = Promise\Utils::unwrap($promises);

$data = [
    'request1' => (string) $responses['request1']->getBody(),
    'request2' => (string) $responses['request2']->getBody(),
];

var_dump($data);

echo "::: It took " . time() - $start . " seconds to fetch the resources" . PHP_EOL;

