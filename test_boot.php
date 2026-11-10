<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Kernel::class);

$request = Request::capture();

try {
    $response = $kernel->handle($request);
    echo 'STATUS: '.$response->getStatusCode()."\n";
    echo "BODY (first 500 chars):\n".substr($response->getContent(), 0, 500)."\n";
    $kernel->terminate($request, $response);
} catch (Throwable $e) {
    echo 'ERROR: '.get_class($e).': '.$e->getMessage()."\n";
    echo 'FILE: '.$e->getFile().':'.$e->getLine()."\n";
}
