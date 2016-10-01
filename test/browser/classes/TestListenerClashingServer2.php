<?php

namespace halfer\SpiderlingUtils\Test;

class TestListenerClashingServer2 extends TestListenerClashingServer1
{
	protected function getSuffix()
	{
		return 2;
	}
}
