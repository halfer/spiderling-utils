<?php

namespace halfer\SpiderlingUtils\Feature;

use halfer\SpiderlingUtils\Server;

trait TestListener
{
	protected $hasInitialised = false;
	protected $servers = [];

	public function runningBrowserTests()
	{
		if (!$this->hasInitialised)
		{
			$this->checkPhpExtensions();
			$this->setupServers();

			/* @var $server Server */
			foreach ($this->servers as $server)
			{
				$this->forkToStartServer($server);
				$this->checkServer($server);
			}

			// This need not be a property of Servers - they are either all up or not
			$this->hasInitialised = true;
		}
	}

	protected function forkToStartServer(Server $server)
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
			$this->markAsChildProcess();
			$this->startServer($server);

			// Exit to prevent PHPUnit thinking it should run again
			exit();
		}
	}

	protected function startServer(Server $server)
	{
		// Assemble parameters
		$domain = escapeshellarg(str_replace('http://', '', $server->getServerUri()));
		$docRoot = escapeshellarg($server->getDocRoot());
		$router = escapeshellarg($server->getRouterScriptPath());
		$pidPath = escapeshellarg($server->getServerPidPath());
		$params = "$domain $docRoot $pidPath $router";

		// Escape any spaces for the command
		$scriptPath = '"' . $server->getServerScriptPath() . '"';
		$command = escapeshellcmd(trim("$scriptPath $params")) . ' 2> /dev/null';

		// Put in a shell command prefix if one is required
		if ($shell = $server->getShellCommand())
		{
			$command = "$shell $command";
		}

		// Pass this command to the shell
		$this->executeShellCommand($command);
	}

	protected function executeShellCommand($command)
	{
		$output = null;
		exec($command, $output);

		// If there is any output, we got an error
		if ($output)
		{
			$this->notifyServerStartError($output[0]);
		}
	}

	/**
	 * Handle an error experienced during server start
	 *
	 * It doesn't seem we can stop the tests from here, even by throwing an exception,
	 * so let's just render something to stdout
	 *
	 * @param string $error
	 */
	protected function notifyServerStartError($error)
	{
		echo sprintf("\nError when running server script: `%s`\n", $error);
	}

	/**
	 * Marks a process as a forked child
	 *
	 * @global boolean $isChildProcess
	 */
	protected function markAsChildProcess()
	{
		global $isChildProcess;

		$isChildProcess = true;
	}

	/**
	 * Retrieves whether a process is a forked child
	 *
	 * @global boolean $isChildProcess
	 * @return boolean
	 */
	protected function isChildProcess()
	{
		global $isChildProcess;

		return isset($isChildProcess) && $isChildProcess;
	}

	protected function checkServer(Server $server)
	{
		// By default there is no server check
		$uri = $server->getCheckAliveUri();
		if (!$uri)
		{
			return;
		}

		// Let's wait a litle for it to settle down
		sleep(3);

		// Check the web server
		$response = file_get_contents($uri);
		if ($response != $server->getCheckAliveExpectedResponse())
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
			foreach ($this->servers as $server)
			{
				$this->destroyServer($server);
			}
		}
	}

	/**
	 * Called to kill a server process
	 *
	 * @todo I'm adding the error suppression operator on the unlink, because the clashing
	 * server test seems to result in this being called when the PID file does not exist -
	 * investigate?
	 *
	 * @param \halfer\SpiderlingUtils\Server $server
	 */
	protected function destroyServer(Server $server)
	{
		// Get pid from temp location
		if (file_exists($filename = $server->getServerPidPath()))
		{
			$pid = (int) file_get_contents($filename);
			if ($pid)
			{
				$this->killProcessById($server, $pid);
			}
			@unlink($filename);
		}
	}

	/**
	 * Override this if the posix functions are not available
	 *
	 * @param $server Server
	 * @param integer $pid
	 */
	protected function killProcessById(Server $server, $pid)
	{
		posix_kill($pid, SIGKILL);
		if ($server->getVerboseExit())
		{
			echo sprintf("Terminating server %d\n", $pid);
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
	 * This must be overrided to create servers to spin up
	 *
	 * Servers are created by instantiating the "Server" class, and passing them to
	 * addServer().
	 */
	abstract protected function setupServers();

	/**
	 * Adds a server to the start-up list
	 *
	 * @todo Swap the exceptions for a more specific one
	 *
	 * @param \halfer\SpiderlingUtils\Server $server
	 */
	protected function addServer(Server $server)
	{
		// Check if there is a URI clash
		/* @var $existingServer Server */
		foreach ($this->servers as $existingServer)
		{
			if ($server->getServerUri() === $existingServer->getServerUri())
			{
				throw new \Exception(
					"Clashing server URIs detected"
				);
			}

			if ($server->getServerPidPath() === $existingServer->getServerPidPath())
			{
				throw new \Exception(
					"Clashing server PID paths detected"
				);
			}
		}

		$this->servers[] = $server;
	}
}
