<?php

namespace halfer\SpiderlingUtils\Demo;

class TestListener extends \halfer\SpiderlingUtils\TestListener
{
	public function switchOnBySuiteName($name)
	{
		return (strpos($name, 'halfer\\SpiderlingUtils\\Demo\\') !== false);
	}
}
