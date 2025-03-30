<?php
declare(strict_types=1);

require_once dirname(__DIR__).'/vendor/autoload.php';

define('BENCHMARK_DURATION', 30);       // seconds
define('BENCHMARK_CONCURRENCY', 100);

echo sprintf('Starting benchmark (duration: %d s, concurrency: %d)...', BENCHMARK_DURATION, BENCHMARK_CONCURRENCY).PHP_EOL;

$totalRequests = 0;
$totalDuration = 0;
$benchmarkStartTime = microtime(true);
while(microtime(true) - $benchmarkStartTime < BENCHMARK_DURATION){
  $start = microtime(true);
  for($i = 0; $i < BENCHMARK_CONCURRENCY; $i++){
    file_get_contents('http://localhost:1337');
  }
  
  $totalRequests += BENCHMARK_CONCURRENCY;
  $totalDuration += microtime(true) - $start;
  
  gc_collect_cycles();
  gc_mem_caches();
}

echo sprintf('%d requests, %f/s', $totalRequests, $totalRequests / $totalDuration).PHP_EOL;
