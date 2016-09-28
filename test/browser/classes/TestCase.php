<?php

namespace halfer\SpiderlingUtils\Test;

class TestCase extends \halfer\SpiderlingUtils\TestCase
{
	public function getTargetElement($uri, $id = '#target')
	{
		return $this->visit($uri)->find($id);
	}
}
