<?php
/**
 * - compression: on
 */

declare(strict_types=1);

use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Http\Server;

require_once dirname(__DIR__).'/vendor/autoload.php';

echo sprintf('PHP: %s'.PHP_EOL, phpversion());
echo sprintf('OpenSwoole: %s'.PHP_EOL, phpversion('openswoole'));

$server = new Server('0.0.0.0', 1337);

$server->set([
  'reactor_num' => 1,     // The number of I/O threads to start
  'worker_num' => 1,      // The number of worker processes to start
  'task_worker_num' => 1, // The amount of task workers to start
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
