<?php

require __DIR__ . '/../vendor/autoload.php';

use Amp\Mysql\MysqlConfig;
use Amp\Mysql\MysqlConnectionPool;
use function Amp\async;
use function Amp\Future\await;

$db = new MysqlConnectionPool(MysqlConfig::fromAuthority("localhost:3306", "root", "secret", "concurrency_example"));

$db->query("DROP TABLE IF EXISTS tmp1, tmp2");

$db->query("CREATE TABLE IF NOT EXISTS tmp1 (value INT)");
$db->query("CREATE TABLE IF NOT EXISTS tmp2 (value INT)");

$statement1 = $db->prepare("INSERT INTO tmp1 VALUES (?)");
$statement2 = $db->prepare("INSERT INTO tmp2 VALUES (?)");

$insertFutures = [];
foreach (\range(1, 10) as $num) {
    $insertFutures[] = async(fn () => $statement1->execute([$num]));
    $insertFutures[] = async(fn () => $statement2->execute([$num]));
}

await($insertFutures);

$countFutures[] = async(fn() => $db->query("SELECT count(*) c FROM tmp1"));
$countFutures[] = async(fn() => $db->query("SELECT count(*) c FROM tmp2"));

[$count1, $count2] = await($countFutures);

var_dump([
    'tmp1' => $count1->fetchRow()['c'],
    'tmp2' => $count2->fetchRow()['c'],
]);

$db->query("DROP TABLE tmp1, tmp2");
$db->close();
