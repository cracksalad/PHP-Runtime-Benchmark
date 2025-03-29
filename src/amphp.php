<?php
/**
 * - compression: on
 * - per IP connection limit: disabled for localhost, 10 otherwise
 */

declare(strict_types=1);

use function Amp\ByteStream\getStdout;
use Amp\Http\HttpStatus;
use Amp\Http\Server\DefaultErrorHandler;
use Amp\Http\Server\Request;
use Amp\Http\Server\RequestHandler;
use Amp\Http\Server\Response;
use Amp\Http\Server\SocketHttpServer;
use Amp\Log\ConsoleFormatter;
use Amp\Log\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\PsrLogMessageProcessor;

require_once dirname(__DIR__).'/vendor/autoload.php';

$logHandler = new StreamHandler(getStdout());
$logHandler->pushProcessor(new PsrLogMessageProcessor());
$logHandler->setFormatter(new ConsoleFormatter());

$logger = new Logger('server');
$logger->pushHandler($logHandler);

$requestHandler = new class() implements RequestHandler {
  public function handleRequest(Request $request) : Response {
    return new Response(
        status: HttpStatus::OK,
        headers: ['Content-Type' => 'text/plain'],
        body: 'Hello, world!',
    );
  }
};

$errorHandler = new DefaultErrorHandler();

$server = SocketHttpServer::createForDirectAccess($logger);
$server->expose('127.0.0.1:1337');
$server->start($requestHandler, $errorHandler);

// Serve requests until SIGINT or SIGTERM is received by the process.
Amp\trapSignal([SIGINT, SIGTERM]);

$server->stop();
