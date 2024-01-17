<?php

class Task
{
    private Fiber $fiber;

    public function __construct(
        private int $id,
        callable $callable
    ) {
        $this->fiber = new Fiber($callable);
    }

    public function run() {
        if (!$this->fiber->isStarted()) {
            return $this->fiber->start();
        }

        return $this->fiber->resume();
    }

    public function getReturn() {
        return $this->fiber->getReturn();
    }

    public function getId(): int {
        return $this->id;
    }

    public function isFinished(): bool {
        return $this->fiber->isTerminated();
    }
}

class Scheduler
{
    private int $maxTaskId = 0;
    private SplQueue $taskQueue;
    private array $taskMap = [];
    private array $returnMap = [];

    public function __construct() {
        $this->taskQueue = new SplQueue();
    }

    public function newTask(callable $callable): Task {
        $taskId = ++$this->maxTaskId;
        $task = new Task($taskId, $callable);
        $this->taskMap[$taskId] = $task;

        $this->schedule($task);

        return $task;
    }

    public function schedule(Task $task): void {
        $this->taskQueue->enqueue($task);
    }

    public function getReturnMap(): array {
        return $this->returnMap;
    }

    public function run(): void {
        while (!$this->taskQueue->isEmpty()) {
            $task = $this->taskQueue->dequeue();
            $task->run();

            echo "... Executing task {$task->getId()}" . PHP_EOL;

            if ($task->isFinished()) {
                echo "::: Task {$task->getId()} has finished" . PHP_EOL;
                $this->returnMap[$task->getId()] = $task->getReturn();
                unset($this->taskMap[$task->getId()]);
            } else {
                $this->schedule($task);
            }
        }
    }
}

function fetchResource(string $url): string {
    $request = curl_init();
    curl_setopt($request, CURLOPT_URL, $url);
    curl_setopt($request, CURLOPT_HEADER, 0);
    curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);

    $curlHandle = curl_multi_init();
    curl_multi_add_handle($curlHandle, $request);
    curl_multi_select($curlHandle);

    $stillRunning = null;

    do {
        $status = curl_multi_exec($curlHandle, $stillRunning);

        if ($stillRunning) {
            Fiber::suspend();
        }
    } while ($stillRunning && $status == CURLM_OK);

    $value = curl_multi_getcontent($request);

    return $value;
}


$url = 'http://localhost:9000/slow.php';

$scheduler = new Scheduler();
$scheduler->newTask(fn () => fetchResource($url));
$scheduler->newTask(fn () => fetchResource($url));

$start = time();

$scheduler->run();

var_dump($scheduler->getReturnMap());

echo "::: It took " . time() - $start . " seconds to fetch the resources" . PHP_EOL;
