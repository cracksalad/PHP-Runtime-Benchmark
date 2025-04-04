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
echo sprintf('Swoole: %s'.PHP_EOL, swoole_version());

$server = new Server('0.0.0.0', 1337);

$server->on('Request', function(Request $request, Response $response): void {
  $response->setStatusCode(200);
  $response->setHeader('Content-Type', 'text/plain');
  $response->end('Hello, World!');
});

$server->start();
