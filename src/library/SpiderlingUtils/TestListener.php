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
			$this->checkPhpExtensions();
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
		// Assemble parameters
		$domain = escapeshellarg(str_replace('http://', '', $this->getTestDomain()));
		$docRoot = escapeshellarg($this->getDocRoot());
		$router = escapeshellarg($this->getRouterScriptPath());
		$pidPath = escapeshellarg($this->getServerPidPath());
		$params = "$domain $docRoot $pidPath $router";

		// Escape any spaces for the command
		$scriptPath = '"' . $this->getServerScriptPath() . '"';
		$command = escapeshellcmd(trim("$scriptPath $params")) . ' 2> /dev/null';

		$output = $return = null;
		exec($command, $output, $return);

		if ($return)
		{
			throw new \Exception(
				sprintf("Failure when running server script `%s`", $scriptPath)
			);
		}
	}

    protected function checkServer()
	{
		// By default there is no server check
		$url = $this->getCheckAliveUrl();
		if (!$url)
		{
			return;
		}

		// Let's wait a litle for it to settle down
		sleep(3);

		// Check the web server
		$response = file_get_contents($url);
		if ($response != $this->getCheckAliveExpectedResponse())
		{
			throw new \Exception(
				"Did not get expected result when checking the web server is up"
			);
		}
	}

	protected function checkPhpExtensions()
	{
		// This is used to fork
		if (!extension_loaded('pcntl'))
		{
			echo "Extension pcntl not loaded";
			exit(1);
		}

		// This is used to kill processes
		if (!extension_loaded('posix'))
		{
			echo "Extension posix not loaded";
			exit(1);
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
			if (file_exists($filename = $this->getServerPidPath()))
			{
				$pid = (int) file_get_contents($filename);
				if ($pid)
				{
					// The PID we have is for the server launch script, not the server itself,
					// so we need to search for the immediate child of the script
					$return = null;
					exec("pgrep -P $pid", $return);
					$serverPid = isset($return[0]) ? (int) $return[0] : null;
					if ($serverPid)
					{
						$this->killProcessById($serverPid);
					}
					unlink($filename);
				}
			}
		}
	}

	/**
	 * Override this if the posix functions are not available
	 *
	 * @param integer $pid
	 */
	protected function killProcessById($pid)
	{
		posix_kill($pid, SIGKILL);
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
	 * This must be overrided to provide the path of the web application's docroot
	 */
	abstract protected function getDocRoot();

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
	 * Override this to enable the routing script feature
	 *
	 * @return string|boolean
	 */
	protected function getRouterScriptPath()
	{
		return false;
	}

	/**
	 * Returns a server URL
	 *
	 * @return string|false
	 */
	protected function getCheckAliveUrl()
	{
		return false;
	}

	/**
	 * Returns the string that a server check should return
	 *
	 * (This is usually just in the test harness, and is not baked into the app under test).
	 *
	 * @return string
	 */
	protected function getCheckAliveExpectedResponse()
	{
		return 'OK';
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
		return $this->getProjectRoot() . '/src/scripts/server.sh';
	}

	/**
	 * Fetches a writeable path location suitable for writing PIDs
	 */
	protected function getServerPidPath()
	{
		return '/tmp/spiderling-phantom.server.pid';
	}

	/**
	 * Gets the root path of this library
	 *
	 * Note that in a Composer context, this does not get the root of the client project,
	 * just the root of this one. I've therefore made this private, so child classes implement
	 * something more suitable for themselves.
	 *
	 * @return string
	 */
	private function getProjectRoot()
	{
		return realpath(__DIR__ . '/../../..');
	}
}
