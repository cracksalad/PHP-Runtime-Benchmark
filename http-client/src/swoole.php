<?php
declare(strict_types=1);

use Swoole\Coroutine;
use Swoole\Coroutine\Http\Client;
use OpenSwoole\Core\Coroutine\WaitGroup;

require_once dirname(__DIR__).'/vendor/autoload.php';

define('BENCHMARK_DURATION', 30);       // seconds
define('BENCHMARK_CONCURRENCY', 100);

echo sprintf('Starting benchmark (duration: %d s, concurrency: %d)...', BENCHMARK_DURATION, BENCHMARK_CONCURRENCY).PHP_EOL;

$totalRequests = 0;
$totalDuration = 0;
$benchmarkStartTime = microtime(true);
while(microtime(true) - $benchmarkStartTime < BENCHMARK_DURATION){
  $start = microtime(true);
  co::run(function(): void {
    $wg = new WaitGroup();
    for($i = 0; $i < BENCHMARK_CONCURRENCY; $i++){
      $wg->add();
      Coroutine::create(function() use($wg) {
        $client = new Client('localhost', 80);
        $client->get('/');
        $client->body;
        $client->close();
        $wg->done();
      });
    }
    $wg->wait();
  });
  
  $totalRequests += BENCHMARK_CONCURRENCY;
  $totalDuration += microtime(true) - $start;
    
  gc_collect_cycles();
  gc_mem_caches();
}
  
echo sprintf('%d requests, %f/s', $totalRequests, $totalRequests / $totalDuration).PHP_EOL;
