#!/bin/bash
#
# Parameters:
#
# $1 = Web server address (required)
# $2 = Document root (required, can be empty)
# $3 = PID path (required)
# $4 = Router script (optional)

# Save pwd and then change dir to the script location
STARTDIR=`pwd`
cd `dirname $0`/../..

# Create a temporary file to catch stderr in
ERROR_FILE=`mktemp /tmp/spiderling-utils-XXXXXXXX`

# Only add a -t (docroot) if it is required
DOC_ROOT=''
if [ ! "$2" = "" ]; then
	DOC_ROOT="-t $2"
fi

# The router script can just be tacked on the end
ROUTER_SCRIPT=$4

# Start up built-in web server with router script
php -S $1 $DOC_ROOT $ROUTER_SCRIPT 2> $ERROR_FILE &

# Let the server settle down
sleep 1

# If there was an error, let's see it on stdout, then exit
if [[ -s $ERROR_FILE ]]; then
	cat $ERROR_FILE
	rm $ERROR_FILE
	exit 1
fi

# Save the 'last backgrounded process' PID to file
echo $! > $3

# Tidy up
rm $ERROR_FILE

# Go back to original dir
cd $STARTDIR
