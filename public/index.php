<?php

use Illuminate\Http\Request;

// Suppress PDO deprecation warnings for PHP 8.5+ compatibility
// These come from Laravel's vendor files and will be fixed in future Laravel updates
if (version_compare(PHP_VERSION, '8.5', '>=')) {
    error_reporting(E_ALL & ~E_DEPRECATED);
}

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
(require_once __DIR__.'/../bootstrap/app.php')
    ->handleRequest(Request::capture());
