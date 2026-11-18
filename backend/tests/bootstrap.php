<?php

declare(strict_types=1);

$_ENV['APP_ENV'] = $_SERVER['APP_ENV'] = 'test';
$_ENV['APP_DEBUG'] = $_SERVER['APP_DEBUG'] = '1';

require_once \dirname(__DIR__).'/vendor/autoload.php';
