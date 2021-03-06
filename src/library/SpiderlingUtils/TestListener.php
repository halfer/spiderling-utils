<?php

namespace halfer\SpiderlingUtils;

abstract class TestListener extends \PHPUnit_Framework_BaseTestListener
{
	use \halfer\SpiderlingUtils\Feature\TestListener;

	public function startTestSuite(\PHPUnit_Framework_TestSuite $suite)
	{
		// This will hear of whole suites being run, or individual tests
		if ($this->switchOnBySuiteName($suite->getName()))
		{
			$this->runningBrowserTests();
		}
	}
}
