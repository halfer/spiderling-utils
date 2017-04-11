<?php

/**
 * Called before PHPUnit runs the unit tests
 */

// Loads this sub-projects own dependecies
$projectRoot = realpath(__DIR__ . '/..');
require_once $projectRoot . '/vendor/autoload.php';

// Loads the parent project as if it were a Composer dependency pointing to dev-master
$parentRoot = realpath($projectRoot . '/../../..');
require_once $parentRoot . '/src/library/SpiderlingUtils/Server.php';
require_once $parentRoot . '/src/library/SpiderlingUtils/Feature/TestCase.php';
require_once $parentRoot . '/src/library/SpiderlingUtils/Feature/TestListener.php';
require_once $parentRoot . '/src/library/SpiderlingUtils/NamespacedTestCase.php';
require_once $parentRoot . '/src/library/SpiderlingUtils/NamespacedTestListener.php';

// Load test classes
require_once $projectRoot . '/classes/TestListener.php';
