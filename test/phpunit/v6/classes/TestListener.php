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
		$docRoot = realpath(__DIR__ . '/../../..') . '/browser/docroot';
		$server = new \halfer\SpiderlingUtils\Server($docRoot);
		$this->addServer($server);
	}
}
