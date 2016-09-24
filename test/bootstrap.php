<?php

/* 
 * Bootstrap file for PHPUnit tests
 */

$projectRoot = realpath(__DIR__ . '/..');
require_once $projectRoot . '/vendor/autoload.php';

$testClassPath = $projectRoot . '/test/browser/classes';
require_once $testClassPath . '/TestCase.php';
require_once $testClassPath . '/TestListenerSingleServer.php';
require_once $testClassPath . '/TestListenerMultipleServers.php';
