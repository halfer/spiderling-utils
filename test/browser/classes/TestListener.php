<?php

namespace halfer\SpiderlingUtils\Demo;

use \halfer\SpiderlingUtils\Server;

class TestListener extends \halfer\SpiderlingUtils\TestListener
{
	public function switchOnBySuiteName($name)
	{
		return (strpos($name, 'halfer\\SpiderlingUtils\\Demo\\') !== false);
	}

	protected function setupServers()
	{
		// Create a server definition
		$testFolder = realpath(__DIR__ . '/../../../test');
		$server = new Server($testFolder . '/browser/docroot');
		$server->setRouterScriptPath($testFolder . '/browser/scripts/router.php');
		$server->setCheckAliveUri('/server-check');

		// Add the server to the list of servers to start
		$this->addServer($server);
	}

	/**
	 * This must be overrided to provide the path of the web application's docroot
	 */
	protected function getDocRoot()
	{
		return realpath(__DIR__ . '/../../../test/browser/docroot');
	}

	/**
	 * Enable the use of a router script by supplying a path
	 */
	protected function getRouterScriptPath()
	{
		return realpath(__DIR__ . '/../../../test/browser/scripts/router.php');
	}

	/**
	 * Turn on check to ensure the web app is alive and minimally working
	 *
	 * @return string
	 */
	public function getCheckAliveUrl()
	{
		return $this->getTestDomain() . '/server-check';
	}
}
