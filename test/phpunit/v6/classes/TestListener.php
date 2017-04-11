<?php

namespace halfer\SpiderlingUtils\NamespaceDemo;

use halfer\SpiderlingUtils\NamespacedTestListener;

class TestListener extends NamespacedTestListener
{
	public function switchOnBySuiteName($name)
	{
		return (strpos($name, 'halfer\\SpiderlingUtils\\NamespaceDemo\\') !== false);
	}

	public function setupServers()
	{
		// Re-use the docroot for Spiderling Utils itself
		$browserTestsRoot = realpath(__DIR__ . '/../../..') . '/browser';
		$server = new \halfer\SpiderlingUtils\Server($browserTestsRoot . '/docroot');

		// Re-use the Spiderling routing script (it contains the check-alive response)
		$server->setRouterScriptPath($browserTestsRoot . '/scripts/router.php');
		$server->setCheckAliveUri('/server-check');

		$this->addServer($server);
	}
}
