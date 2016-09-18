<?php

namespace halfer\SpiderlingUtils;

abstract class TestListener extends \PHPUnit_Framework_BaseTestListener
{
	protected $hasInitialised = false;

	public function startTestSuite(\PHPUnit_Framework_TestSuite $suite)
	{
		// This will hear of whole suites being run, or individual tests
		if ($this->switchOnBySuiteName($suite->getName()))
		{
			$this->runningBrowserTests();
		}
	}

	public function runningBrowserTests()
	{
		if (!$this->hasInitialised)
		{
			$this->touchPhantomLog();
			$this->forkToStartServer();
			$this->hasInitialised = true;
			$this->checkServer();
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

	protected function forkToStartServer()
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
			$this->startServer();

			// Exit to prevent PHPUnit thinking it should run again
			exit();
		}
	}

	protected function startServer()
	{
		exec($this->getServerScriptPath() . ' 2> /dev/null');
	}

    protected function checkServer()
	{
		// Let's wait a litle for it to settle down
		sleep(3);

		// Check the web server
		$response = file_get_contents($this->getTestDomain() . '/server-check');
		if ($response != 'OK')
		{
			throw new \Exception(
				"Did not get expected result when checking the web server is up"
			);
		}
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

	/**
	 * This must be overrided to determine when to start the web server (if at all)
	 *
	 * For example, if the user just runs their unit tests, they won't want a server to start
	 * up - so here they can listen for a suite or test namespace part that indicates that
	 * a web server is required.
	 */
	abstract protected function switchOnBySuiteName($name);

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
	 * Override this to specify the PhantomJS logging path
	 *
	 * @return string
	 */
	protected function getLogPath()
	{
		return '/tmp/spiderling-phantom.log';
	}

	/**
	 * Override this to specify a different shell script to start up the web server
	 *
	 * @return string
	 */
	protected function getServerScriptPath()
	{
		return $this->getProjectRoot() . '/test/browser/scripts/server.sh';
	}

	protected function getProjectRoot()
	{
		return realpath(__DIR__ . '/../../..');
	}
}
