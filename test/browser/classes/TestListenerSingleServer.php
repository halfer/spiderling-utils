<?php

namespace halfer\SpiderlingUtils\Demo;

use \halfer\SpiderlingUtils\Server;

class TestListenerSingleServer extends \halfer\SpiderlingUtils\TestListener
{
	public function switchOnBySuiteName($name)
	{
		return (strpos($name, 'halfer\\SpiderlingUtils\\Test\\PageTest') !== false);
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
}
