<?php

namespace halfer\SpiderlingUtils\Demo;

class TestListener extends \halfer\SpiderlingUtils\TestListener
{
	public function switchOnBySuiteName($name)
	{
		return (strpos($name, 'halfer\\SpiderlingUtils\\Demo\\') !== false);
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
