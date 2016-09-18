#!/bin/bash
#
# @todo Need to pass router script path in as an argument
# @todo Need to pass web root path in as an argument

# Save pwd and then change dir to the script location
STARTDIR=`pwd`
cd `dirname $0`/../../..

# Start up built-in web server
php \
	-S 127.0.0.1:8090 \
	-t ../../web \
	../../test/browser/scripts/router.php \
	2> /dev/null

# Go back to original dir
cd $STARTDIR
