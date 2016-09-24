<?php

namespace halfer\SpiderlingUtils\Test;

class PageTest extends TestCase
{
	/**
	 * Checks that text injected by JavaScript is working (and hence that PhantomJS is working)
	 *
	 * @driver phantomjs
	 */
	public function testJavaScriptPhrase()
	{
		$target = $this->getTargetElement($this->getTestDomain());
		$this->assertEquals('Event successful', $target->text());
	}
}
