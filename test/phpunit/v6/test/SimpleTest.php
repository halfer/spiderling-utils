<?php

namespace halfer\SpiderlingUtils\NamespaceDemo;

use halfer\SpiderlingUtils\NamespacedTestCase;

class SimpleTest extends NamespacedTestCase
{
	/**
	 * @driver simple
	 */
	public function testAnything()
	{
		$text = $this->visit('http://localhost:8090/')->find('p')->text();
		$this->assertContains('JavaScript', $text);
	}
}
