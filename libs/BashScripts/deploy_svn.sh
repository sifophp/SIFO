#!/bin/bash
# Albert Lombarte
# alombarte@gmail.com
# This script updates the SIFO code to the latest and the passed instance as well.
# It is meant to update your code in production.

SCRIPTPATH=$(cd ${0%/*} && echo $PWD/${0##*/})
CORE=`dirname "$SCRIPTPATH"`
CORE=`cd "${CORE}/../.." && pwd -P`

if [ $# != 1 ]
then
	echo "This script updates the server with the latest code in the repo"
	echo "--USAGE: $0 <instancename>"
	exit 0
fi

INSTANCE=$1
CHANGELOG="$CORE/changelog.txt"
USER=`whoami`
INSTANCEPATH="$CORE/instances/$INSTANCE"
TODAY=`date "+%Y-%b-%d %k:%M"`

#if [ "$USER" == "root" ]
#then
#        clear
#        echo -e "Do not use root for updating code"
#        exit 0
#fi

echo "Updating servers..."
echo "************************************************" >> $CHANGELOG
echo "$INSTANCE update: $TODAY ($USER)" >> $CHANGELOG
echo "************************************************" >> $CHANGELOG

echo "From revisions:" >> $CHANGELOG
svn info $CORE | grep Revision >> $CHANGELOG
svn info $INSTANCEPATH | grep Revision >> $CHANGELOG
echo "To:" >> $CHANGELOG
echo "Updating $CORE"
svn update $CORE >> $CHANGELOG
echo "Updating $INSTANCEPATH"
svn update $INSTANCEPATH >> $CHANGELOG
clear
tail -n 100 $CHANGELOG
