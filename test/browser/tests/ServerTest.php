<?php

namespace halfer\SpiderlingUtils\Test;

use \halfer\SpiderlingUtils\Server;

/** 
 * Tests for the Server class
 */
class ServerTest extends TestCase
{
	/**
	 * Checks the file existence validation on the router script path
	 *
	 * @expectedException \Exception
	 */
	public function testValidateRouterScriptPath()
	{
		$server = new Server(null);
		$server->setRouterScriptPath(__DIR__ . '/does-not-exist.php');
	}

	/**
	 * Checks the file existence validation on the router script path
	 *
	 * @expectedException \Exception
	 */
	public function testValidateServerScriptPath()
	{
		$server = new Server(null);
		$server->setServerScriptPath(__DIR__ . '/does-not-exist.sh');
	}

	public function testNoDocRoot()
	{
		$server = new Server();
		$this->assertNull($server->getDocRoot());

		$listener = new TestListenerHarness();
		$listener->startServer($server);

		// Use CSV parser on $listener->command to split this up to ensure param 2 is ''
		$elements = str_getcsv($listener->command, " ");
		$this->assertTrue(
			isset($elements[2]) && $elements[2] = "''",
			"Check that the docroot parameter to the start script is empty"
		);
	}

	/**
	 * Ensures that two servers with the same URL are rejected
	 *
	 * @expectedException \Exception
	 */
	public function testClashingServerUris()
	{
		$listener = new TestListenerMultipleServersHarness();
		$listener->addServers([
			new Server('/tmp/docroot1', 'http://localhost:10000/'),
			new Server('/tmp/docroot2', 'http://localhost:10000/'),
		]);
		$listener->runningBrowserTests();
	}

	/**
	 * Ensures that two servers with the same PID path are rejected
	 *
	 * @expectedException \Exception
	 */
	public function testClashingServerPidPaths()
	{
		$server1 = new Server('/tmp/docroot1', 'http://localhost:10000/');
		$server1->setServerPidPath('/tmp/pidpath');
		$server2 = new Server('/tmp/docroot2', 'http://localhost:10001/');
		$server2->setServerPidPath('/tmp/pidpath');
		$listener = new TestListenerMultipleServersHarness();
		$listener->addServers([$server1, $server2, ]);
		$listener->runningBrowserTests();
	}
}

class TestListenerHarness extends \halfer\SpiderlingUtils\TestListener
{
	public $command;

	// Dummy method
	protected function switchOnBySuiteName($name)
	{
		return false;
	}

	// Dummy method
	protected function setupServers()
	{
	}

	// Make this method public
	public function startServer(Server $server)
	{
		parent::startServer($server);
	}

	// Overrides the parent in order to capture the command string
	public function executeShellCommand($command)
	{
		$this->command = $command;
	}
}

class TestListenerMultipleServersHarness extends \halfer\SpiderlingUtils\TestListener
{
	// Dummy method
	protected function switchOnBySuiteName($name)
	{
		return false;
	}

	// Dummy method
	protected function setupServers()
	{
	}

	public function addServers(array $servers)
	{
		foreach ($servers as $server)
		{
			$this->addServer($server);
		}
	}

	protected function forkToStartServer(Server $server)
	{
		// Overrided this for safety
	}
}
