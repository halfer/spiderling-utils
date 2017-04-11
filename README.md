Spiderling Utils
===

[![Build Status](https://api.travis-ci.org/halfer/spiderling-utils.svg)](https://travis-ci.org/halfer/spiderling-utils)

Introduction
---

This is a small library to help quickly spin up a PHP project on testing with [PHPUnit Spiderling](https://github.com/OpenBuildings/phpunit-spiderling).

Spiderling is an extension to PHPUnit and allows the developer writing tests to switch between drivers for each test. The supported drivers are PhantomJS, Selenium and PHP/cURL. The first two are most interesting from an integration testing perspective: they utilise real browsers, and so tests dependent on JavaScript will still work.

Spiderling Utils makes it easy to integrate this system. It offers these features:

* Spins up the built-in PHP web server
* Allows PhantomJS logging to be enabled
* Adds some settle-down time when PhantomJS is connecting, for test stability
* Adds a simple way to output screenshots inside logs on headless build servers e.g. Travis
* Uses a test listener to only start up the web server if it is required
* Some extra test methods, such as waiting for a redirect, and waiting for a selector count

Installation
---

Add this clause in your `composer.json`:

    "repositories": [
        {
            "type": "vcs",
            "url":  "https://github.com/halfer/spiderling-utils.git"
        }
    ]

Then using Composer, run this:

    composer require --dev halfer/spiderling-utils:0.1.*

Usage
---

Create an abstract class to inherit from `\halfer\SpiderlingUtils\TestCase`, and that will become your test case parent.

	abstract class TestCase extends \halfer\SpiderlingUtils\TestCase
	{
		/**
		 * Optional, only if you want to override the default test domain
		 */
		protected function getTestDomain()
		{
			return 'http://127.0.0.1:10000';
		}

		/**
		 * Optional, only required if you want to override the default Phantom log path
		 *
		 * Use null/false here to turn off logging entirely
		 */
		protected function getLogPath()
		{
			return '/path/to/my/phantom.log';
		}

		/**
		 * Optional, only override if you want to change the location of the PNG screenshot output
		 **/
		protected function getScreenshotRawPath()
		{
			return '/path/to/my/spiderling-screenshot.png';
		}

		/**
		 * Optional, only override if you want to change the location of base64 screenshot output
		 *
		 * This is useful on headless build servers, where dumping the screenshot output to stdout
		 * is the easiest way to get it to your local machine.
		 **/
		protected function getScreenshotEncodedPath()
		{
			return '/path/to/my/spiderling-base64-screenshot.txt';
		}
	}

Create a class to inherit from `\halfer\SpiderlingUtils\TestListener`, and that will become a listener that can be wired into your `phpunit.xml`. This must implement `switchOnBySuiteName($name)`, which should return true if a suite name or namespace is one that you recognise, and if a web server is required. This means that if you only need to run your unit tests, a server is not spun up.

You must also implement`setupServers()`, which is your test's opportunity to declare what servers to spin up. In most cases, you will create just one, but if you need more than one (e.g. to avoid session conflict) then you can create as many as you like.

	class TestListener extends \halfer\SpiderlingUtils\TestListener
	{
		/**
		 * Required, return true if you recognise the test suite name or namespace
		 *
		 * Returning true turns on the internal web server
		 **/
		protected function switchOnBySuiteName($name)
		{
			return (strpos($name, 'Foo\\Baar\\') !== false);
		}

		/**
		 * Here's how to spin up a single server
		 */
		protected function setupServers()
		{
			$docRoot = realpath(__DIR__ . '/../../..') . '/web';
			$server = new \halfer\SpiderlingUtils\Server($docRoot);
			$this->addServer($server);
		}
	}

Spiderling Utils contains its own working example of a configuration file featuring a listener, [see here](https://github.com/halfer/spiderling-utils/blob/master/phpunit.xml).

If you wish, you can create a simple routing PHP script. The purpose of this is to connect the
web server to your app, making small interventions to:

* Set your app's environment to a test mode if you wish;
* Answer a special URL to detect if the system is up;
* Detect static file requests so they can be passed straight to the web server;

The tests for Spiderling Utils have their own routing file, [see here](https://github.com/halfer/spiderling-utils/blob/master/test/browser/scripts/router.php).

Server configuration
---

There are a number of configuration setters in the Server class that can be used to modify its behaviour; here is the full set:

	// The docroot is mandatory here, you can optionally supply the server URI too, in the second param
	$server = new \halfer\SpiderlingUtils\Server($docRoot);
	$server = new \halfer\SpiderlingUtils\Server($docRoot, '127.0.0.1:10000');

	// Points to the optional routing script, defaults to off
	$server->setRouterScriptPath('/path/to/router.php');

	// Reset the server URI, the default is 127.0.0.1:8090
	$server->setServerUri('127.0.0.1:10000');

	// A path to append to the server URI to test that it is up, e.g. /server-test. Defaults to off
	$server->setCheckAliveUri('/status');

	// A string to expect from the alive test, defaults to "OK"
	$server->setCheckAliveExpectedResponse('{ "status": "OK" }');

	// Points to the server start-up script
	$server->setServerScriptPath('/path/to/server.sh');

	// Points to the location to store the PID for this server
	$server->getServerPidPath('/path/to/server.pid');

Writing browser tests
---

There are plenty of examples on searching for DOM elements using CSS, retrieving text items, filling in controls, clicking on buttons, submitting forms, etc. See [the manual here](https://github.com/OpenBuildings/spiderling). To write test classes, see the [manual here](https://github.com/OpenBuildings/phpunit-spiderling).

Here is a short example:

	namespace Foo\Tests\Integration;

	use Foo\Tests\Integration\TestCase;

	class IntegrationTest extends TestCase
	{
		const URL_BASE = 'http://127.0.0.1:8090';

		/**
		 * Tests something
		 *
		 * @driver simple
		 */
		public function testSomething()
		{
			$text = $this->
				visit(self::URL_BASE . '/test.html')->
				find('div.hello')->
				text();
			$this->assertContains('Hello', $text);
		}
	}

You can see that:

* A fully-qualified local server URL is visited
* A div of a specific class is found
* The text in that div is rendered to a string
* A PHPUnit "contains" comparison is run on the string
* A driver name is specified in the test docblock, this is required

Two drivers are tested for use with Spiderling Utils:

* `simple` is a curl-based HTTP fetch
* `phantomjs` spins up a PhantomJS server (assuming you have the right dependencies) so that JavaScript can be run as per a standard browser

There are two others, `selenium` and `kohana`, but they are not tested. See the PHPUnit Spiderling docs for more information on those.

Using Spiderling with Travis
---

PHPUnit Spiderling works just fine on Travis, see [an example configuration here](https://github.com/halfer/spiderling-utils/blob/master/.travis.yml).

Requirements
---

The PHP requirement for this system is determined by the highest minimum requirement of its dependencies. Presently that is `symfony/css-selector`, which requires 5.5.9.

In terms of operating system, I am presently testing on Ubuntu 14.04, and would expect any modern GNU/Linux distro to be fine. OS X should be fine too. Some work would be required on Windows, not least to replace the shell script with something else, but I think it could be persuaded to work!

Self tests
---

To run the internal tests, enter this on your console:

    composer install
    PATH=$PATH:`pwd`/vendor/bin ./vendor/bin/phpunit

Status
---

This library is presently an **early alpha**, which I am using in a couple of personal projects. It is subject to change, but if anyone wishes to use it as it stands, I have tagged a v0.1 release.

I wonder whether, if developers already have their test class inheritance trees set up already, whether the `TestCase` class would be better as a trait.

To-do items
---

* If there is no docroot _or_ a router script, throw an error - at least one is required
* Test that the server start script, the router script and the docroot may contain spaces
* Fix missing test to turn off logging
* Test file existence exceptions in Server class
* Can we have a simple automatic path for PID files (e.g. /tmp/spiderling/127.0.0.1_8090.pid)?
  - Maybe by supporting {SERVER} and {PORT} in the pid path?
* Is there a way to detect conflicting server ports or PID paths across different listeners?
  - E.g. read the phpunit.xml config, instantiate each listener, call `addServers()` and then scan the server list of each one
* Document the base64 image decode script and how to use this on a headless build server
* Spiderling and PHPUnit Spiderling both support PHP 5.3 and 5.4 - supporting these too should probably be easy?
* Add MIT license file
