#!/bin/bash
#
# Parameters:
#
# $1 = Web server address (required)
# $2 = Document root (required)
# $3 = Router script (optional)

# Save pwd and then change dir to the script location
STARTDIR=`pwd`
cd `dirname $0`/../..

# Start up built-in web server without router script
if [ "$3" = "" ]; then
	php -S $1 -t $2      2> /dev/null
else
	php -S $1 -t $2 $3   2> /dev/null
fi;

# Go back to original dir
cd $STARTDIR
