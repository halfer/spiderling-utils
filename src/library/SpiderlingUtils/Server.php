<?php

namespace halfer\SpiderlingUtils;


/**
 * Contains a definition of a test server
 */

class Server
{
	// These can't really have sensible default values
	protected $docRoot;

	// All of these have default values
	protected $serverUri = 'http://127.0.0.1:8090';
	protected $routerScriptPath = false;
	protected $checkAliveUri = false;
	protected $expectedResponse = 'OK';
	protected $logPath = '/tmp/spiderling-phantom.log';
	protected $serverPidPath = '/tmp/spiderling-phantom.server.pid';

	// Defaults for these are set in the c'tor
	protected $serverScriptPath;

	public function __construct($docRoot, $serverUri = null)
	{
		$this->docRoot = $docRoot;
		if ($serverUri)
		{
			$this->serverUri = $serverUri;
		}
		$this->serverScriptPath = $this->getProjectRoot() . '/src/scripts/server.sh';
	}

	public function getServerUri()
	{
		return $this->serverUri;
	}

	public function setServerUri($serverUri)
	{
		$this->serverUri = $serverUri;
	}

	public function getRouterScriptPath()
	{
		return $this->routerScriptPath;
	}

	public function setRouterScriptPath($routerScriptPath)
	{
		$this->routerScriptPath = $routerScriptPath;
	}

	public function getCheckAliveUri()
	{
		return $serverUri . '/' . $this->checkAliveUri;
	}

	public function setCheckAliveUri($checkAliveUri)
	{
		$this->checkAliveUri = $checkAliveUri;
	}

	public function getCheckAliveExpectedResponse()
	{
		return $this->expectedResponse;
	}

	public function setCheckAliveExpectedResponse($expectedResponse)
	{
		$this->expectedResponse = $expectedResponse;
	}

	public function getLogPath()
	{
		return $this->logPath;
	}

	public function setLogPath($logPath)
	{
		$this->logPath = $logPath;
	}

	public function getServerScriptPath()
	{
		return $this->serverScriptPath;
	}

	public function setServerScriptPath($serverScriptPath)
	{
		$this->serverScriptPath = $serverScriptPath;
	}

	public function getServerPidPath()
	{
		return $this->serverPidPath;
	}

	public function setServerPidPath($serverPidPath)
	{
		$this->serverPidPath = $serverPidPath;
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