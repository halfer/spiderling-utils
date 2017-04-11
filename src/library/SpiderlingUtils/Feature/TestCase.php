<?php

/**
 * A trait to bring in utility methods into our class structure
 */

namespace halfer\SpiderlingUtils\Feature;

use Openbuildings\Spiderling\Driver_Phantomjs_Connection;
use halfer\SpiderlingUtils\Driver_Phantomjs;

trait TestCase
{
	/**
	 * Let's set add some logging here, to see why PhantomJS is flaky on Travis
	 */
	public function driver_phantomjs()
	{
		$this->checkPhantomIsAvailable();
		$this->touchPhantomLog();

		// We can supply a log location here (or omit to use /dev/null)
		$connection = new Driver_Phantomjs_Connection();
		$connection->start(null, $this->getLogPath() ?: '/dev/null');

		$driver = new Driver_Phantomjs();
		$driver->connection($connection);

		$this->waitUntilPhantomStarts($connection);

		return $driver;
	}

	/**
	 * Throws a fatal exception if the PhantomJS executable is not found
	 * 
	 * @throws \Exception
	 */
	protected function checkPhantomIsAvailable()
	{
		$output = $return = null;
		exec('which phantomjs', $output, $return);
		if ($return)
		{
			throw new \Exception("Can't find 'phantomjs' - does the PATH include it?");
		}
	}

	/**
	 * Create a new log file for PhantomJS, mainly useful for Travis
	 */
	protected function touchPhantomLog()
	{
		if ($logPath = $this->getLogPath())
		{
			touch($logPath);
		}
	}

	/**
	 * Try waiting to see if Travis can be made more robust
	 * 
	 * @param Driver_Phantomjs_Connection $connection
	 */
	protected function waitUntilPhantomStarts(Driver_Phantomjs_Connection $connection)
	{
		$i = 0;
		while (!$connection->is_running() || !$connection->is_running())
		{
			usleep(200000);
			if ($i++ > 10)
			{
				break;
			}
		}
	}

	/**
	 * Waits until a redirect or a timeout occurs
	 * 
	 * @param string $originalUrl
	 * @return boolean Success
	 */
	protected function waitUntilRedirected($originalUrl)
	{
		$retry = 0;
		do {
			$hasRedirected = $originalUrl != $this->current_url();
			if (!$hasRedirected)
			{
				usleep(500000);
			}
		} while (!$hasRedirected && $retry++ < 20);

		return $hasRedirected;
	}

	/**
	 * Wait until the count of a selector agrees with the expected count, or a timeout occurs
	 * 
	 * @param string $selector
	 * @param integer $expectedCount
	 * @return boolean Success
	 */
	protected function waitForSelectorCount($selector, $expectedCount)
	{
		$retry = 0;
		do {
			$count = count($this->all($selector));
			$isReached = $count == $expectedCount;
			if (!$isReached)
			{
				usleep(500000);
			}
		} while (!$isReached && $retry++ < 20);

		return $isReached;
	}

	/**
	 * Takes a screenshot and appends it to a base64 log file
	 * 
	 * This is really handy for Travis, where exporting build artefacts like screenshots is
	 * not all that easy to do. We just cat the log file to the screen after the build, paste
	 * it into a file, and decode it locally.
	 * 
	 * @param string $title
	 */
	protected function encodedScreenshot($title)
	{
		$file = $this->getScreenshotRawPath();
		$this->screenshot($file);
		$this->base64out($file, $title);
		unlink($file);
	}

	/**
	 * Base 64 encoding helper
	 * 
	 * @param string $filename
	 * @param string $title
	 * @throws \Exception
	 */
	protected function base64out($filename, $title)
	{
		if (!file_exists($filename))
		{
			throw new \Exception("File '$filename' not found");
		}

		$data = 
			"-----\n" .
			$title . "\n" .
			"-----\n" .
			chunk_split(base64_encode(file_get_contents($filename))) .
			"-----\n\n";
		file_put_contents($this->getScreenshotEncodedPath(), $data, FILE_APPEND);
	}

	/**
	 * Override this to change the test domain in use
	 *
	 * This gets the default server URI, which should match up with whatever is being used
	 * in the Server instance in use. If multiple Servers are in use then it is the developer's
	 * responsibility to store/manage their list of URIs in the best way they see fit.
	 *
	 * @return string
	 */
	protected function getTestDomain()
	{
		return 'http://127.0.0.1:8090';
	}

	/**
	 * Override this to specify the PhantomJS logging path, or use false to turn off
	 *
	 * @return string
	 */
	protected function getLogPath()
	{
		return '/tmp/spiderling-phantom.log';
	}

	protected function getScreenshotRawPath()
	{
		return '/tmp/spiderling-screenshot.log';
	}

	protected function getScreenshotEncodedPath()
	{
		return '/tmp/spiderling-screenshot-data.log';
	}
}
