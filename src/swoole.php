<?php
declare(strict_types=1);

use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Http\Server;

require_once dirname(__DIR__).'/vendor/autoload.php';

$server = new Server('127.0.0.1', 1337);

$server->set([
  'worker_num' => 4,      // The number of worker processes to start
  'task_worker_num' => 4, // The amount of task workers to start
  'backlog' => 128,       // TCP backlog connection number
]);

$server->on('Request', function(Request $request, Response $response): void {
  $response->setStatusCode(200);
  $response->setHeader('Content-Type', 'text/plain');
  $response->end('Hello, World!');
});

$server->on('Task', function(Server $server, int $task_id, int $reactorId, mixed $data): void {
  // is not expected to be called
  echo "Task Worker Process received data";
  
  echo "#{$server->worker_id}\tonTask: [PID={$server->worker_pid}]: task_id=$task_id, data_len=" . strlen($data) . "." . PHP_EOL;
  
  $server->finish($data);
});

$server->start();
