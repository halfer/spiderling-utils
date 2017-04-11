#!/bin/bash
#
# Script to do installs depending on PHP version running

phpenv version-name
composer install --working-dir ${PROJECTROOT}/test/phpunit/v6