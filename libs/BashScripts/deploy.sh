#!/bin/bash
# Albert Lombarte
# alombarte@gmail.com
# This script updates the SIFO code to the latest and the passed instance as well.
# It is meant to update your code in production.

ORIGINAL_PATH=$PWD
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
CHANGELOG="$CORE/instances/$INSTANCE/logs/deploy_changelog.txt"
USER=`whoami`
INSTANCEPATH="$CORE/instances/$INSTANCE"
TODAY=`date "+%Y-%b-%d %k:%M"`
BRANCH='master'

#if [ "$USER" == "root" ]
#then
#        clear
#        echo -e "Do not use root for updating code"
#        exit 0
#fi

mkdir -p $CORE/instances/$INSTANCE/logs/
echo "Updating servers..."
echo "************************************************" >> $CHANGELOG
echo "$INSTANCE update: $TODAY ($USER)" >> $CHANGELOG
echo "************************************************" >> $CHANGELOG

cd $CORE
CORE_REMOTE_REV=`git ls-remote origin $BRANCH | sed 's/\([0-9a-f]\{10\}\)\(.*\)/\1/g'`
CORE_LOCAL_REV=`git rev-parse refs/heads/$BRANCH | sed 's/\([0-9a-f]\{10\}\)\(.*\)/\1/g'`

cd $INSTANCEPATH
INST_REMOTE_REV=`git ls-remote origin $BRANCH | sed 's/\([0-9a-f]\{10\}\)\(.*\)/\1/g'`
INST_LOCAL_REV=`git rev-parse refs/heads/$BRANCH | sed 's/\([0-9a-f]\{10\}\)\(.*\)/\1/g'`

if [ "$CORE_LOCAL_REV" == "$CORE_REMOTE_REV" ]
then
    echo -e "SIFO is already in the latest revision: $CORE_LOCAL_REV"
    echo -e "SIFO is already in the latest revision: $CORE_LOCAL_REV" >> $CHANGELOG
else
    cd $CORE
    git pull origin $BRANCH >> $CHANGELOG
    echo "What changed in SIFO..." >> $CHANGELOG
    git whatchanged $CORE_LOCAL_REV..$CORE_REMOTE_REV >> $CHANGELOG
fi

if [ "$INST_REMOTE_REV" == "$INST_LOCAL_REV" ]
then
    echo -e "Instance $INSTANCE is already in the latest revision: $INST_REMOTE_REV"
    echo -e "Instance $INSTANCE is already in the latest revision: $INST_REMOTE_REV" >> $CHANGELOG
else
    cd $INSTANCEPATH
    git pull origin $BRANCH >> $CHANGELOG
    echo "What changed in $INSTANCE..." >> $CHANGELOG
    git whatchanged $INST_LOCAL_REV..$INST_REMOTE_REV >> $CHANGELOG
fi

# Put user to original path before executing the script
cd $ORIGINAL_PATH

tail -n 100 $CHANGELOG
