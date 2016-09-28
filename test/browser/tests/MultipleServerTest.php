<?php

namespace halfer\SpiderlingUtils\Test;

/**
 * Test cases to show that multiple servers may be started, which may be useful to run
 * multiple sessions simulataneously, for example.
 */
class MultipleServerTest extends TestCase
{
	/**
	 * Checks that the right number of servers are spun up
	 *
	 * @driver phantomjs
	 */
	public function testAllServersUp()
	{
		$this->assertTrue(
			(bool) $this->getTargetElement('http://127.0.0.1:8091/')
		);
		$this->assertTrue(
			(bool) $this->getTargetElement('http://127.0.0.1:8092/')
		);
	}
}
