<?php
declare(strict_types=1);

$str = file_get_contents(dirname(__DIR__).'/README.md');

$data = [];
// parse input
$lines = explode("\n", $str);
$dataLine = false;
foreach($lines as $line){
  if(str_starts_with($line, '|---')){
    $dataLine = true;
  } elseif($dataLine && empty($line)){
    break;
  } elseif($dataLine && !str_starts_with($line, '//')){
    $cols = explode('|', $line);
    $data[$cols[1]][intval($cols[2])] = [
      'requests' => floatval(str_replace(',', '', $cols[3])),
      'time' => floatval(str_replace(',', '', $cols[4]))
    ];
  }
}

// format output
$requestsPerSecond = [];
$averageResponseTime = [];
foreach($data as $runtime => $results){
  $requestsPerSecond[] = implode('|', [
    $runtime,
    $results[10]['requests'],
    $results[100]['requests'],
    $results[1000]['requests']
  ]);
  $averageResponseTime[] = implode('|', [
    $runtime,
    $results[10]['time'],
    $results[100]['time'],
    $results[1000]['time']
  ]);
}

echo 'Requests Per Second:'.PHP_EOL;
echo 'Runtime|Concurrency 10|Concurrency 100|Concurrency 1000'.PHP_EOL;
echo implode("\n", $requestsPerSecond).PHP_EOL;
echo PHP_EOL;
echo 'Average Response Time:'.PHP_EOL;
echo 'Runtime|Concurrency 10|Concurrency 100|Concurrency 1000'.PHP_EOL;
echo implode("\n", $averageResponseTime).PHP_EOL;
