<?php

namespace halfer\SpiderlingUtils\Test;

use \halfer\SpiderlingUtils\Server;

class TestListenerMultipleServers extends \halfer\SpiderlingUtils\TestListener
{
	public function switchOnBySuiteName($name)
	{
		return (strpos($name, 'halfer\\SpiderlingUtils\\Test\\MultipleServerTest') !== false);
	}

	protected function setupServers()
	{
		// Create two servers, avoiding the default 8090 port, which we've already used
		$this->addServer($this->createServer(8091));
		$this->addServer($this->createServer(8092));
	}

	protected function createServer($port)
	{
		$testFolder = realpath(__DIR__ . '/../../../test');
		$server = new Server($testFolder . '/browser/docroot', 'http://127.0.0.1:' . $port);
		$server->setRouterScriptPath($testFolder . '/browser/scripts/router.php');
		$server->setCheckAliveUri('/server-check');

		return $server;
	}
}
