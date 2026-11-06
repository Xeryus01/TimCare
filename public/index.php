<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

$appRoot = dirname(__DIR__, 2).DIRECTORY_SEPARATOR.'timcare-dev';
if (!is_file($appRoot.DIRECTORY_SEPARATOR.'artisan')) {
    $appRoot = dirname(__DIR__);
}

if (file_exists($maintenance = $appRoot.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

require $appRoot.'/vendor/autoload.php';

(require_once $appRoot.'/bootstrap/app.php')
    ->handleRequest(Request::capture());
