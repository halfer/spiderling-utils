<?php

namespace halfer\SpiderlingUtils\Test;

/**
 * Tests to show the screenshot system in PhantomJS is working correctly
 */
class ScreenshotTest extends TestCase
{
	use FileTrait;

	/**
	 * Ensures that the screenshot system in PhantomJS is working
	 *
	 * @driver phantomjs
	 */
	public function testScreenshot()
	{
		// Visit our default page
		$this->visit($this->getTestDomain());

		// Remove any existing screenshots and then make a new one
		$imagePath = '/tmp/spiderling-utils-page.png';
		$this->removeIfExists($imagePath);
		$this->screenshot($imagePath);

		// Check that the screenshot produced a file
		$this->assertGreaterThan(
			0,
			file_exists($imagePath) ? filesize($imagePath) : 0,
			"Check that the screenshot operation produces a non-empty file"
		);

		// Clean up after ourselves
		$this->removeIfExists($imagePath);
	}
}
