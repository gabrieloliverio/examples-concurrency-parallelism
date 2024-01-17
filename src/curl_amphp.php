<?php

require __DIR__ . '/../vendor/autoload.php';

use function Amp\delay;

function fetchResource(string $url, string $id) {
    $request = curl_init();
    curl_setopt($request, CURLOPT_URL, $url);
    curl_setopt($request, CURLOPT_HEADER, 0);
    curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);

    $curlHandle = curl_multi_init();
    curl_multi_add_handle($curlHandle, $request);
    curl_multi_select($curlHandle);

    $stillRunning = null;

    do {
        echo "... {$id}" . PHP_EOL;
        $status = curl_multi_exec($curlHandle, $stillRunning);

        if ($stillRunning) {
            echo "... Suspending {$id}" . PHP_EOL;
            delay(0.5);
        }
    } while ($stillRunning && $status == CURLM_OK);

    echo "::: {$id} has finished" . PHP_EOL;

    return curl_multi_getcontent($request);
}

$start = time();

$url = 'http://localhost:9000/slow.php';
$future1 = Amp\async(fn() => fetchResource($url, 'Request 1'));
$future2 = Amp\async(fn() => fetchResource($url, 'Request 2'));


list($errors, $values) = Amp\Future\awaitAll([$future1, $future2]);
var_dump($values);

echo "::: It took " . time() - $start . " seconds to fetch the resources" . PHP_EOL;
