Spiderling Utils
===

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

Then in your `require-dev` section add this:

    "halfer/spiderling-utils": "dev-master"

Then issue a `composer update` in the usual way.

Usage
---

Create an abstract class to inherit from `\halfer\SpiderlingUtils\TestCase`, and that will become your test case parent.

	class TestCase extends \halfer\SpiderlingUtils\TestCase
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

Create a class to inherit from `\halfer\SpiderlingUtils\TestListener`, and that will become a listener that can be wired into your phpunit.xml. This must implement `switchOnBySuiteName($name)`, which should return true if a suite name or namespace is one that you recognise, and if a web server is required. This means that if you only need to run your unit tests, a server is not spun up.

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
		 * Optional, return a test URL if your router/app supports a test method
		 */
		protected function getCheckAliveUrl()
		{
			return $this->getTestDomain() . '/server-check';
		}

		/**
		 * Optional, return a string for the check-alive feature (defaults to "OK")
		 */
		protected function getCheckAliveExpectedResponse()
		{
			return 'Working';
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
		 * Optional, override this if you want to point to a different web server start script
		 */
		protected function getServerScriptPath()
		{
			return $this->getProjectRoot() . '/path/to/my/server.sh';
		}

		/**
		 * Optional, override this if you want to change the location of the server PID file
		 */
		protected function getServerPidPath()
		{
			return '/tmp/spiderling-phantom.server.pid';
		}
	}

Now, you'll need to create a simple routing file. The purpose of this is to connect the PHP
web server to your app, making small interventions to:

* Set your app's environment to a test mode if you wish;
* Detect static file requests so they can be passed straight to the web server;
* Answer a special URL to detect if the system is up

The tests for Spiderling Utils have their own routing file, [see here](https://github.com/halfer/spiderling-utils/blob/master/test/browser/scripts/router.php).

Writing browser tests
---

There are plenty of examples on searching for DOM elements using CSS, retrieving text items, filling in controls, clicking on buttons, submitting forms, etc. See [the manual here](https://github.com/OpenBuildings/spiderling).

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

This library is presently an **early alpha**, which I am using in a couple of personal projects. It is subject to change, but if anyone wishes to use it as it stands, I am happy to tag a release.

I wonder whether, if developers already have their test class inheritance trees set up already, whether the `TestCase` class would be better as a trait.

README to do
---

* Pass parameters to the server script (router path, web root path)
* Move the PID responsibility from the router to the server script, add a param for that too
* Make router script optional
* Create a Travis build to show it working
* Add build icons in the GitHub README
* Check that a build without `require-dev` deps does not trigger post-install scripts (which would fail)
* Show how the listener can be wired into phpunit.xml
* Log file path is specified in both classes, can we centralise this?
* Add MIT license file
