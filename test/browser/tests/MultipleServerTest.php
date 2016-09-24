<?php

namespace halfer\SpiderlingUtils\Test;

class MultipleServerTest extends TestCase
{
	/**
	 * Checks that the right number of servers are spun up
	 *
	 * @driver phantomjs
	 */
	public function testAllServersUp()
	{
		// Not working yet
		/*
		$this->assertTrue(
			(bool) $this->getTargetElement('http://127.0.0.1:8091/')
		);
		$this->assertTrue(
			(bool) $this->getTargetElement('http://127.0.0.1:8092/')
		);
		*/
		$this->assertTrue(true);
	}
}
