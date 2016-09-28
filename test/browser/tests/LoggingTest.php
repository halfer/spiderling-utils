<?php

namespace halfer\SpiderlingUtils\Test;

/**
 * Tests to show the PhantomJS logging options are working correctly
 */

class LoggingTest extends TestCase
{
	use FileTrait;

	/**
	 * Ensures that the log file is growing when we run PhantomJS operations
	 *
	 * @driver phantomjs
	 */
	public function testLoggingIsWorking()
	{
		// Zap the file to start with
		$logPath = $this->getLogPath();
		$this->zeroFile($logPath);

		// Visit our test-specific page
		$url = $this->getTestDomain() . '/logging-test.php';
		$this->getTargetElement($url, '#log-test');

		// Ensure that the log file contains things we expect
		$logLines = file_get_contents($logPath);
		$this->assertContains(
			$url,
			$logLines,
			"Check that the log file contains our URL"
		);
		$this->assertContains(
			'log-test',
			$logLines,
			"Check that the log file contains our element ID"
		);
	}

	/**
	 * Specify a custom log file that can be handled/deleted independently
	 *
	 * @return string
	 */
	public function getLogPath()
	{
		return '/tmp/phantomjs-LoggingTest.log';
	}

	/**
	 * Checks that no logging is done if we have switched it off
	 *
	 * Do I need to just use a separate test class, with getLogPath() set to null? Need to
	 * investigate this.
	 */
	public function testNotLoggingIsWorking()
	{
		// FIXME
		$this->markTestIncomplete();
	}
}
