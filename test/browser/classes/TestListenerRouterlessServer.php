<?php

namespace halfer\SpiderlingUtils\Test;

use \halfer\SpiderlingUtils\Server;

class TestListenerRouterlessServer extends \halfer\SpiderlingUtils\TestListener
{
	/**
	 * Turns on this single server if any of the named test classes are encountered
	 *
	 * @param string $name
	 * @return boolean
	 */
	public function switchOnBySuiteName($name)
	{
		$found = false;
		$triggerTests = ['RouterlessTest', ];
		foreach ($triggerTests as $triggerTest)
		{
			$found = strpos($name, 'halfer\\SpiderlingUtils\\Test\\' . $triggerTest) !== false;
			if ($found)
			{
				break;
			}
		}

		return $found;
	}

	protected function setupServers()
	{
		// Create a routerless server definition
		$testFolder = realpath(__DIR__ . '/../../../test');
		$server = new Server($testFolder . '/browser/docroot', 'http://127.0.0.1:8093');

		// Add the server to the list of servers to start
		$this->addServer($server);
	}
}
