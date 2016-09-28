<?php

namespace halfer\SpiderlingUtils;

class Driver_Phantomjs extends \Openbuildings\Spiderling\Driver_Phantomjs
{
	public function __destruct()
	{
		/*
		 * If we spin up multiple servers, we can get a race condition situation where all
		 * Phantom destructors are called at the same time. So, they all appear to be up, and
		 * so each tries to send a closedown request, only for one of them to fail. This
		 * protects against this nicely.
		 *
		 * For reference, the error is:
		 *
		 *   Curl "session" throws exception Failed to connect to localhost port 4446: Connection
		 *   refused
		 */
		try
		{
			parent::__destruct();
		}
		catch (\Openbuildings\Spiderling\Exception_Driver $e)
		{
			// Do nothing in this case
		}
		catch (\Exception $e)
		{
			// Let's see other errors
			throw $e;
		}
	}
}
