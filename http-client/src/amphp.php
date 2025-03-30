<?php
declare(strict_types=1);

use Amp\Http\Client\HttpClientBuilder;
use Amp\Http\Client\Request;
use function Amp\async;
use function Amp\Future\await;

require_once dirname(__DIR__).'/vendor/autoload.php';

define('BENCHMARK_DURATION', 30);       // seconds
define('BENCHMARK_CONCURRENCY', 100);

$client = HttpClientBuilder::buildDefault();
$handler = static function(string $url) use($client): string {
  $response = $client->request(new Request($url));
  return $response->getBody()->buffer();
};

echo sprintf('Starting benchmark (duration: %d s, concurrency: %d)...', BENCHMARK_DURATION, BENCHMARK_CONCURRENCY).PHP_EOL;

$totalRequests = 0;
$totalDuration = 0;
$benchmarkStartTime = microtime(true);
while(microtime(true) - $benchmarkStartTime < BENCHMARK_DURATION){
  $futures = new \SplFixedArray(BENCHMARK_CONCURRENCY);
  
  $start = microtime(true);
  for($i = 0; $i < BENCHMARK_CONCURRENCY; $i++){
    $futures[$i] = async(fn() => $handler('http://localhost:1337'));
  }
  await($futures);
  
  $totalRequests += BENCHMARK_CONCURRENCY;
  $totalDuration += microtime(true) - $start;
  
  gc_collect_cycles();
  gc_mem_caches();
}

echo sprintf('%d requests, %f/s', $totalRequests, $totalRequests / $totalDuration).PHP_EOL;
