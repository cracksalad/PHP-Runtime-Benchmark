<?php
declare(strict_types=1);

require_once dirname(__DIR__).'/vendor/autoload.php';

http_response_code(200);
header('Content-Type: text/plain');
echo 'Hello, world!';
