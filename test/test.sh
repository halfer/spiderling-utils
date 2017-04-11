#!/bin/bash
#
# Script to do tests depending on PHP version running

# Run the standard tests
${PROJECTROOT}/vendor/bin/phpunit

# If there were errors then exit with failure
if [[ $? -ne 0 ]]; then
	exit 1
fi

# Now for the v6 tests
VERSION=`phpenv version-name`
SUBROOT=${PROJECTROOT}/test/phpunit

# Version 6 of PHPUnit is just for PHP 7.0+
if ([ "$VERSION" == "7.0" ] || [ "$VERSION" == "7.1" ]); then

	echo "PHP 7.x, running extra tests"
	${SUBROOT}/v6/vendor/bin/phpunit --configuration ${SUBROOT}/v6/

	# If there were errors then exit with failure
	if [[ $? -ne 0 ]]; then
		exit 1
	fi
else
	echo "Not PHP 7.x, nothing extra to test"
fi
