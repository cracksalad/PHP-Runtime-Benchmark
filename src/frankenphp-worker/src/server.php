<?php
declare(strict_types=1);

// Handler outside the loop for better performance (doing less work)
$handler = static function(): void {
  // Called when a request is received,
  // superglobals, php://input and the like are reset
  http_response_code(200);
  header('Content-Type: text/plain');
  echo 'Hello, world!';
};

$maxRequests = (int)($_SERVER['MAX_REQUESTS'] ?? 0);
for($nbRequests = 0; !$maxRequests || $nbRequests < $maxRequests; ++$nbRequests) {
  $keepRunning = \frankenphp_handle_request($handler);
  
  // Do something after sending the HTTP response
  // nothing to do here!
  
  // Call the garbage collector to reduce the chances of it being triggered in the middle of a page generation
  gc_collect_cycles();
  
  if(!$keepRunning) break;
}
