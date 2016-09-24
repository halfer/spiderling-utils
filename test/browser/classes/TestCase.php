<?php

namespace halfer\SpiderlingUtils\Test;

class TestCase extends \halfer\SpiderlingUtils\TestCase
{
	public function getTargetElement($uri)
	{
		return $this->visit($uri)->find('#target');
	}
}
