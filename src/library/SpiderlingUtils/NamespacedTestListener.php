<?php

namespace halfer\SpiderlingUtils;

abstract class NamespacedTestListener extends \PHPUnit\Framework\BaseTestListener
{
	use \halfer\SpiderlingUtils\Feature\TestListener;

	public function startTestSuite(\PHPUnit\Framework\TestSuite $suite)
	{
		// This will hear of whole suites being run, or individual tests
		if ($this->switchOnBySuiteName($suite->getName()))
		{
			$this->runningBrowserTests();
		}
	}
}
