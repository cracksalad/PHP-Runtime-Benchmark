<?php
declare(strict_types=1);

use Nyholm\Psr7\Response;
use Nyholm\Psr7\Factory\Psr17Factory;
use Spiral\RoadRunner\Worker;
use Spiral\RoadRunner\Http\PSR7Worker;

require_once dirname(__DIR__).'/vendor/autoload.php';

$worker = Worker::create();

$factory = new Psr17Factory();

$psr7 = new PSR7Worker($worker, $factory, $factory, $factory);

while(true) {
  try {
    $request = $psr7->waitRequest();
    if($request === null) {
      break;
    }
  } catch(\Throwable $e) {
    $psr7->respond(new Response(400));
    continue;
  }
  
  try {
    $psr7->respond(new Response(200, ['Content-Type' => 'text/plain'], 'Hello, world!'));
  } catch(\Throwable $e) {
    $psr7->respond(new Response(500, [], 'Something Went Wrong!'));
    $psr7->getWorker()->error((string) $e);
  }
}
