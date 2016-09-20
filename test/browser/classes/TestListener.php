<?php

namespace halfer\SpiderlingUtils\Demo;

class TestListener extends \halfer\SpiderlingUtils\TestListener
{
	public function switchOnBySuiteName($name)
	{
		return (strpos($name, 'halfer\\SpiderlingUtils\\Demo\\') !== false);
	}

	/**
	 * This must be overrided to provide the path of the web application's docroot
	 */
	protected function getDocRoot()
	{
		return realpath(__DIR__ . '/../../../test/browser/docroot');
	}

	/**
	 * Turn on check to ensure the web app is alive and minimally working
	 *
	 * @return string
	 */
	public function getCheckAliveUrl()
	{
		return $this->getTestDomain() . '/server-check';
	}
}
