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
	 * @todo Assert the log contains the custom URL
	 * @todo Switch to testing a special id, and ensure that is mentioned as well
	 *
	 * @driver phantomjs
	 */
	public function testLoggingIsWorking()
	{
		// Zap the file to start with
		$logPath = $this->getLogPath();
		$this->zeroFile($logPath);

		// Visit our test-specific page
		$this->getTargetElement($this->getTestDomain() . '/logging-test.php');

		// Get the size of the log file after we've visited a page
		$endSize = $this->getFilesize($logPath);

		// Ensure that the log file has grown
		$this->assertGreaterThan(
			0,
			$endSize,
			"Check that the log file has had some lines added to it"
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
