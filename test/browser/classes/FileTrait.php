<?php

namespace halfer\SpiderlingUtils\Test;

/**
 * Useful file methods to bring into tests
 */

trait FileTrait
{
	protected function removeIfExists($path)
	{
		if (file_exists($path))
		{
			unlink($path);
		}
	}
}
