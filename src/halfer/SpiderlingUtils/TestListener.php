<?php

namespace halfer\SpiderlingUtils;

class TestListener extends \PHPUnit_Framework_BaseTestListener
{
	protected $hasInitialised = false;

	public function startTestSuite(\PHPUnit_Framework_TestSuite $suite)
	{
		// This will hear of whole suites being run, or individual tests
		if (
			($suite->getName() == 'browser') ||
			(strpos($suite->getName(), 'Awooga\\Testing\\Browser\\') !== false)
		)
		{
			$this->runningBrowserTests();
		}
	}

	public function runningBrowserTests()
	{
		if (!$this->hasInitialised)
		{
			$this->touchPhantomLog();
			$this->startServer();
			$this->hasInitialised = true;
			$this->checkServer();
			$this->removeSearchIndex();
		}
	}

	/**
	 * Create a new log file for PhantomJS, mainly useful for Travis
	 */
	protected function touchPhantomLog()
	{
		touch('/tmp/phantom-awooga.log');
	}

	protected function startServer()
	{
		$pid = pcntl_fork();
		if ($pid == -1)
		{
			die('Could not fork');
		}
		elseif ($pid)
		{
			// We are the parent. We do not wait for the child to exit, as it never will - so
			// it is killed at the end.
		}
		else
		{
			// We are the child. We use exec() to create a new process with its output redirected
			// to null. This in turn is used to start the PHP web server. We can't use pcntl_exec
			// as that would prevent us from hiding stdout/stderr output - the web server is
			// pretty verbose.
			exec(
				$this->getProjectRoot() . '/test/browser/scripts/server.sh 2> /dev/null'
			);

			// Exit to prevent PHPUnit thinking it should run again
			exit();
		}
	}

	protected function checkServer()
	{
		// Let's wait a litle for it to settle down
		sleep(3);

		// Check the web server
		$response = file_get_contents(TestCase::DOMAIN . '/server-check');
		if ($response != 'OK')
		{
			throw new \Exception(
				"Did not get expected result when checking the web server is up"
			);
		}
	}

	protected function removeSearchIndex()
	{
		system('rm -rf ' . $this->getProjectRoot() . '/filesystem/tmp/search-index');
	}

	/**
	 * If the web server was started, let's kill it
	 */
	public function __destruct()
	{
		if ($this->hasInitialised)
		{
			// Get pid from temp location
			if (file_exists($filename = $this->getProjectRoot() . '/.server.pid'))
			{
				$pid = (int) file_get_contents($filename);
				if ($pid)
				{
					posix_kill($pid, SIGKILL);
					unlink($filename);
				}
			}
		}
	}

	protected function getProjectRoot()
	{
		return realpath(__DIR__ . '/../../..');
	}
}
