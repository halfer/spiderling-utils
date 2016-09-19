<?php
 
 /*
  * Simple routing file for browser testing. This is started up by the
  * server.sh script.
  */
 
$root = realpath(__DIR__ . '/../../..');

// Save our process ID for later termination
file_put_contents('/tmp/spiderling-phantom.server.pid', getmypid());

if (preg_match('#^/assets/#', $_SERVER["REQUEST_URI"]))
{
	// Let static assets fall through to the default server
	return false;
}
elseif ($_SERVER["REQUEST_URI"] == '/server-check')
{
	// This is a check to ensure the web server is up
	echo 'OK';
}
else
{
	// Send page requests to the web app
	include $root . "/test/browser/docroot/index.php";
}
