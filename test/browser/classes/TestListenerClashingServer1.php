<?php

namespace halfer\SpiderlingUtils\Test;

use \halfer\SpiderlingUtils\Server;

class TestListenerClashingServer1 extends \halfer\SpiderlingUtils\TestListener
{
	public function switchOnBySuiteName($name)
	{
		return (strpos($name, 'halfer\\SpiderlingUtils\\Test\\ClashingServerTest') !== false);
	}

	/**
	 * This and its counterpart will try to bind to the same port
	 */
	protected function setupServers()
	{
		// Create a server definition
		$testFolder = realpath(__DIR__ . '/../../../test');
		$port = 8094;
		$server = new Server($testFolder . '/browser/docroot', 'http://127.0.0.1:' . $port);

		// Device to ensure the two clashing server classes use different PID files
		$suffix = $this->getSuffix();
		$server->setServerPidPath("/tmp/spiderling-phantom-{$port}-{$suffix}.server.pid");

		// Delete any existing error notifications
		@unlink($this->getErrorPathName());

		// Add the server to the list of servers to start
		$this->addServer($server);
	}

	/**
	 * Device to differentiate the PID files of the two clashing servers
	 *
	 * @return int
	 */
	protected function getSuffix()
	{
		return 1;
	}

	/**
	 * Method to intercept the server error output and save it to a file for testing
	 *
	 * @param string $error
	 */
	protected function notifyServerStartError($error)
	{
		file_put_contents($this->getErrorPathName(), $error);
	}

	protected function getErrorPathName()
	{
		$suffix = $this->getSuffix();
		$path = "/tmp/spiderling-utils-clash-{$suffix}.pid";

		return $path;
	}
}
