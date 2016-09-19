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

Create an abstract class to inherit from `SpiderlingUtils\TestCase`, and that will become your test case parent.

	TODO Example here, indicate what is required/mandatory

Create a class to inherit from `SpiderlingUtils\TestListener`, and that will become a listener that can be wired into your phpunit.xml. This must implement `switchOnBySuiteName($name)`, which should return true if a suite name or namespace is one that you recognise, and if a web server is required. This means that if you only need to run your unit tests, a server is not spun up.

	TODO Example here, indicate what is required/mandatory

Now, you'll need to create a simple routing file. The purpose of this is to connect the PHP
web server to your app, making small interventions to:

* Set your app's environment to a test mode if you wish;
* Detect static file requests so they can be passed straight to the web server;
* Answer a special URL to detect if the system is up

The tests for Spiderling Utils have their own routing file, [see here](https://github.com/halfer/spiderling-utils/blob/master/test/browser/scripts/router.php).

Status
---

This library is presently an **early alpha**, which I am using in a couple of personal projects. It is
subject to change, but if anyone wishes to use it as it stands, I am happy to tag a release.

I wonder whether, if developers already have their test class inheritance trees set up already,
whether the `TestCase` class would be better as a trait.

README to do
---

* Pass parameters to the server script (router path, web root path)
* Add test run instructions
* Create a Travis build to show it working
* Add build icons in the GitHub README
* Check that a build without `require-dev` deps does not trigger post-install scripts (which would fail)
* List the configuration methods for each class
* Show how the listener can be wired into phpunit.xml
* Add MIT license file
