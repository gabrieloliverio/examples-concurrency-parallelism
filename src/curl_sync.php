<?php

$urls = ["request_1" => "http://localhost:9000/slow.php", "request_2" => "http://localhost:9000/slow.php"];

$start = time();

$curlMultiHandle = curl_multi_init();
$responses = [];

foreach ($urls as $name => $url) {
    echo "... Sending {$name}" . PHP_EOL;

    $request = curl_init();

    curl_setopt($request, CURLOPT_URL, $url);
    curl_setopt($request, CURLOPT_HEADER, 0);
    curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);
    $responses[$name] = curl_exec($request);
}

echo "::: It took " . time() - $start . " seconds to fetch the resources" . PHP_EOL;

var_dump($responses);
