#!/bin/bash
#
# Script to do tests depending on PHP version running

VERSION=`phpenv version-name`

# Version 6 of PHPUnit is just for PHP 7.0+
if ([ "$VERSION" == "7.0" ] || [ "$VERSION" == "7.1" ]); then
	${PROJECTROOT}/test/phpunit/v6/vendor/bin/phpunit
fi
