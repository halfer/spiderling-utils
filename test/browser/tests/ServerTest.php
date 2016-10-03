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

	public function testClashingServerUris()
	{
		$this->markTestIncomplete();
	}

	public function testClashingServerPidPaths()
	{
		$this->markTestIncomplete();
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
