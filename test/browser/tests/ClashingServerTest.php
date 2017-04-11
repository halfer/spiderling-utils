<?php

namespace halfer\SpiderlingUtils\Test;

/**
 * A test to check that port clashes are detected
 */
class ClashingServerTest extends TestCase
{
	/**
	 * Uses a file-based check to ensure both servers have had a chance to start
	 */
	public function testClashingServers()
	{

		$startTime = microtime(true);
		do
		{
			$loggedCount = 0;
			$failedCount = 0;
			for($suffix = 1; $suffix <= 2; $suffix++)
			{
				$path = "/tmp/spiderling-utils-clash-{$suffix}.pid";
				if (file_exists($path))
				{
					$loggedCount++;
					$error = file_get_contents($path);
					if (strpos($error, 'Address already in use') !== false)
					{
						$failedCount++;
					}
				}
			}
		} while ($loggedCount < 2 && $this->sleepWithLimit($startTime));

		$this->assertEquals(1, $failedCount, "Check that one clashing server failed");
	}

	/**
	 * Helps wait for up to five seconds before giving up
	 *
	 * @param float $startTime
	 * @return boolean
	 */
	protected function sleepWithLimit($startTime)
	{
		$timeNow = microtime(true);
		$carryOn = $timeNow - $startTime < 5;
		if ($carryOn)
		{
			usleep(100000);
		}

		return $carryOn;
	}
}
