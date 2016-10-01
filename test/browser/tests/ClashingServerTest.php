<?php

namespace halfer\SpiderlingUtils\Test;

/**
 * A test to check that port clashes are detected
 */
class ClashingServerTest extends TestCase
{
	public function testClashingServers()
	{
		$succeededCount = $failedCount = 0;

		for($suffix = 1; $suffix <= 2; $suffix++)
		{
			$path = "/tmp/spiderling-utils-clash-{$suffix}.pid";
			if (file_exists($path))
			{
				$error = file_get_contents($path);
				if (strpos($error, 'Address already in use') !== false)
				{
					$failedCount++;
				}
			}
		}

		$this->assertEquals(1, $failedCount, "Check that one clashing server failed");
	}
}
