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
		$element = $this->
			visit($this->getTestDomain())->
			find('#target');
		$this->assertEquals('Event successful', $element->text());
	}
}
