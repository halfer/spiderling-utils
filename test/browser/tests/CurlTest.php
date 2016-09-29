<?php

namespace halfer\SpiderlingUtils\Test;

/** 
 * Checks that curl is available as a working driver too
 */
class CurlTest extends TestCase
{
	/**
	 * Checks the simple driver is working
	 *
	 * @driver simple
	 */
	public function testCurlFetch()
	{
		$target = $this->getTargetElement($this->getTestDomain() . '/curl.php', '#target .target2');
		$this->assertContains(
			'Text',
			$target->text()
		);
	}
}
