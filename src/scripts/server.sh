#!/bin/bash
#
# Parameters:
#
# $1 = Web server address (required)
# $2 = Document root (required)
# $3 = PID path (required)
# $4 = Router script (optional)

# Save PID to file
echo $$ > $3

# Save pwd and then change dir to the script location
STARTDIR=`pwd`
cd `dirname $0`/../..

if [ "$4" = "" ]; then
	# Start up built-in web server without router script
	php -S $1 -t $2      2> /dev/null
else
	# Start up built-in web server with router script
	php -S $1 -t $2 $4   2> /dev/null
fi;

# Go back to original dir
cd $STARTDIR
