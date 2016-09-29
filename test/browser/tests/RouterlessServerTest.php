<?php

namespace halfer\SpiderlingUtils\Test;

/**
 * Tests to check a router is not required
 *
 * Uses the test(s) in the parent test case
 */
class RouterlessServerTest extends PageTest
{
	public function getTestDomain() {
		return 'http://127.0.0.1:8093';
	}
}