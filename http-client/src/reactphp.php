<?php
declare(strict_types=1);

use React\Http\Browser;
use function React\Async\await;
use function React\Promise\all;

require_once dirname(__DIR__).'/vendor/autoload.php';

define('BENCHMARK_DURATION', 30);       // seconds
define('BENCHMARK_CONCURRENCY', 100);

$client = new Browser();

echo sprintf('Starting benchmark (duration: %d s, concurrency: %d)...', BENCHMARK_DURATION, BENCHMARK_CONCURRENCY).PHP_EOL;

$totalRequests = 0;
$totalDuration = 0;
$benchmarkStartTime = microtime(true);
while(microtime(true) - $benchmarkStartTime < BENCHMARK_DURATION){
  $promises = new \SplFixedArray(BENCHMARK_CONCURRENCY);
  
  $start = microtime(true);
  for($i = 0; $i < BENCHMARK_CONCURRENCY; $i++){
    $promises[$i] = $client->get('http://localhost:1337');
  }
  await(all($promises));
  
  $totalRequests += BENCHMARK_CONCURRENCY;
  $totalDuration += microtime(true) - $start;
  
  gc_collect_cycles();
  gc_mem_caches();
}

echo sprintf('%d requests, %f/s', $totalRequests, $totalRequests / $totalDuration).PHP_EOL;
