#!/bin/bash
#
# Script to do tests depending on PHP version running

VERSION=`phpenv version-name`

SUBROOT=${PROJECTROOT}/test/phpunit

# Version 6 of PHPUnit is just for PHP 7.0+
if ([ "$VERSION" == "7.0" ] || [ "$VERSION" == "7.1" ]); then
	${SUBROOT}/v6/vendor/bin/phpunit --configuration ${SUBROOT}/v6/
fi
