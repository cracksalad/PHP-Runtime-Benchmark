<?php
declare(strict_types=1);

use Workerman\Worker;
use Workerman\Connection\TcpConnection;
use Workerman\Protocols\Http\Request;

require_once dirname(__DIR__).'/vendor/autoload.php';

// Create a Worker to listen on port 1337 and use the http protocol for communication
$http_worker = new Worker('http://0.0.0.0:1337');

// Start 1 processes to provide external services
$http_worker->count = 2;

// Reply with "hello world" to the browser when receiving data sent by the browser
$http_worker->onMessage = function(TcpConnection $connection, Request $request): void {
    // Send "Hello, world!" to the browser
    $connection->send('Hello, world!');
};

// Run the worker
Worker::runAll();
