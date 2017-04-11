#!/bin/bash
#
# Script to do installs depending on PHP version running

VERSION=`phpenv version-name`

# Version 6 of PHPUnit is just for PHP 7.0+
if ([ "$VERSION" == "7.0" ] || [ "$VERSION" == "7.1" ]); then
	echo "PHP 7.x, installing extra dependencies"
	composer install --working-dir ${PROJECTROOT}/test/phpunit/v6
else
	echo "Not PHP 7.x, nothing extra to install"
fi
