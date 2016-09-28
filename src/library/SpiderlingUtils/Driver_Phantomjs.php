<?php

namespace halfer\SpiderlingUtils;

class Driver_Phantomjs extends \Openbuildings\Spiderling\Driver_Phantomjs
{
	public function __destruct()
	{
		// Uses is_running instead of is_started, to ensure we don't try to kill it for every
		// server that we spin up
		if ($this->_connection AND $this->_connection->is_running())
		{
			$this->_connection->stop();
		}
	}
}
