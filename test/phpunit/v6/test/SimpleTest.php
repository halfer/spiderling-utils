<?php

namespace halfer\SpiderlingUtils\NamespaceDemo;

use halfer\SpiderlingUtils\NamespacedTestCase;

class SimpleTest extends NamespacedTestCase
{
	/**
	 * @driver simple
	 */
	public function testSimpleDriver()
	{
		$text = $this->visit('http://localhost:8090/')->find('p')->text();
		$this->assertContains('JavaScript', $text);
	}

	/**
	 * @driver phantomjs
	 */
	public function testPhantomDriver()
	{
		$text = $this->visit('http://localhost:8090/')->find('#target')->text();
		$this->assertContains('Event successful', $text);
	}
}
