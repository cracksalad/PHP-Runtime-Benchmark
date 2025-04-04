<?php
declare(strict_types=1);

use React\Http\HttpServer;
use React\Http\Message\Response;

require_once dirname(__DIR__).'/vendor/autoload.php';

$http = new HttpServer(function (Psr\Http\Message\ServerRequestInterface $request) {
  return Response::plaintext("Hello, world!");
});
$socket = new React\Socket\SocketServer('0.0.0.0:1337');
$http->listen($socket);
