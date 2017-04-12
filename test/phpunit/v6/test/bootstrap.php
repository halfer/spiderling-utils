<?php

/**
 * Called before PHPUnit runs the unit tests
 */

// Loads this sub-projects own dependecies
$projectRoot = realpath(__DIR__ . '/..');
require_once $projectRoot . '/vendor/autoload.php';

// Loads the parent project as if it were a Composer dependency pointing to dev-master
$parentRoot = realpath($projectRoot . '/../../..');

// I should be able to use the autoloader in Composer, but that didn't seem to work,
// so using a little custom autoloader for now.
spl_autoload_register(
	function($class) use ($parentRoot)
	{
		if (strpos($class, 'halfer\\SpiderlingUtils\\') !== false)
		{
			$removeNamespace = str_replace('halfer\\', '', $class);
			$path = str_replace('\\', '/', $removeNamespace);
			$fullPath = $parentRoot . "/src/library/{$path}.php";
			require_once $fullPath;
		}
	}
);

// Load test classes
require_once $projectRoot . '/classes/TestListener.php';
