<?php

namespace halfer\SpiderlingUtils\Test;

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
		$server = new \halfer\SpiderlingUtils\Server(null);
		$server->setRouterScriptPath(__DIR__ . '/does-not-exist.php');
	}

	/**
	 * Checks the file existence validation on the router script path
	 *
	 * @expectedException \Exception
	 */
	public function testValidateServerScriptPath()
	{
		$server = new \halfer\SpiderlingUtils\Server(null);
		$server->setServerScriptPath(__DIR__ . '/does-not-exist.sh');
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
