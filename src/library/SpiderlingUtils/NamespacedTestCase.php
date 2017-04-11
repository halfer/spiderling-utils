<?php

namespace halfer\SpiderlingUtils;

use Openbuildings\Spiderling\Driver_Phantomjs;

/**
 * Let's have some declarations for magic methods
 *
 * @method \Openbuildings\Spiderling\Page visit($uri, array $query = array()) Initiate a visit with the currently selected driver
 * @method string content() Return the content of the last request from the currently selected driver
 * @method string current_path() Return the current browser url without the domain
 * @method string current_url() Return the current url
 * @method \Openbuildings\Spiderling\Node assertHasCss($selector, array $filters = array(), $message = NULL)
 * @method \Openbuildings\Spiderling\Node find($selector) Returns a single matching Node
 * @method \Openbuildings\Spiderling\Node not_present($selector) Checks that a CSS expression is not found
 * @method array all($selector) Returns all matching elements as Nodes
 * @method void screenshot($filename) Takes a screenshot at this point in time
 */
abstract class NamespacedTestCase extends \Openbuildings\PHPUnitSpiderling\TestCase
{
	use \halfer\SpiderlingUtils\Feature\TestCase;

	/**
	 * Calls new implementation of the driver (PHP 7, PHPUnit >= 6)
	 */
	public function driver_phantomjs() : Driver_Phantomjs
	{
		return $this->base_driver_phantomjs();
	}
}
