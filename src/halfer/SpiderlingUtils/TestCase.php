<?php

namespace halfer\SpiderlingUtils;

use \Openbuildings\Spiderling\Driver_Phantomjs_Connection;

/**
 * Let's have some declarations for magic methods
 *
 * @method \Openbuildings\Spiderling\Page visit($uri, array $query = array()) Initiate a visit with the currently selected driver
 * @method string content() Return the content of the last request from the currently selected driver
 * @method string current_path() Return the current browser url without the domain
 * @method string current_url() Return the current url
 * @method \Openbuildings\Spiderling\Node assertHasCss($selector, array $filters = array(), $message = NULL)
 * @method \Openbuildings\Spiderling\Node find($selector) Returns a single matching Node
 * @method \Openbuildings\Spiderling\Node not_present($selector) Checks that a CSS expression is not found
 * @method array all($selector) Returns all matching elements as Nodes
 * @method void screenshot($filename) Takes a screenshot at this point in time
 */
abstract class TestCase extends \Openbuildings\PHPUnitSpiderling\Testcase_Spiderling
{
	use \Awooga\Testing\BaseTestCase;

	/**
	 * Let's set add some logging here, to see why PhantomJS is flaky on Travis
	 */
	public function driver_phantomjs()
	{
		$this->checkPhantomIsAvailable();

		// We can supply a log location here (or omit to use /dev/null)
		$logFile = $this->getLogPath();
		$connection = new Driver_Phantomjs_Connection();
		$connection->start(null, $this->getLogMode() ? $logFile : '/dev/null');

		$driver = new \Openbuildings\Spiderling\Driver_Phantomjs();
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
	 * @return string
	 */
	protected function getTestDomain()
	{
		return 'http://127.0.0.1:8090';
	}

	/**
	 * Override this to turn on PhantomJS logging
	 *
	 * @return boolean
	 */
	protected function getLogMode()
	{
		return false;
	}

	/**
	 * Override this to specify the PhantomJS logging path
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
