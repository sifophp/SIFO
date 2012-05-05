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
    echo ""
    echo ""
	echo "This script takes '$CORE' to HEAD revision of the current branches"
	echo "--USAGE: $0 <instancename>"
	echo ""
	exit 0
fi

INSTANCE=$1
LOG="/tmp/deploy_$INSTANCE.log"
CHANGELOG="$CORE/instances/$INSTANCE/logs/deploy_LOG.txt"

USER=`whoami`
INSTANCEPATH="$CORE/instances/$INSTANCE"
TODAY=`date "+%Y-%b-%d %k:%M"`


#if [ "$USER" == "root" ]
#then
#        clear
#        echo -e "Do not use root for updating code"
#        exit 0
#fi

mkdir -p $CORE/instances/$INSTANCE/logs/

echo "Updating servers..." > $LOG
echo "************************************************" >> $LOG
echo "$INSTANCE update: $TODAY ($USER)" >> $LOG
echo "************************************************" >> $LOG

cd $CORE
SIFO_BRANCH=`git branch | awk '/^\*/ { print $2 }'`
CORE_REMOTE_REV=`git ls-remote origin $SIFO_BRANCH | sed 's/\([0-9a-f]\{10\}\)\(.*\)/\1/g'`
CORE_LOCAL_REV=`git rev-parse refs/heads/$SIFO_BRANCH | sed 's/\([0-9a-f]\{10\}\)\(.*\)/\1/g'`

cd $INSTANCEPATH
INSTANCE_BRANCH=`git branch | awk '/^\*/ { print $2 }'`
INST_REMOTE_REV=`git ls-remote origin $INSTANCE_BRANCH | sed 's/\([0-9a-f]\{10\}\)\(.*\)/\1/g'`
INST_LOCAL_REV=`git rev-parse refs/heads/$INSTANCE_BRANCH | sed 's/\([0-9a-f]\{10\}\)\(.*\)/\1/g'`

echo "---SIFO---"
echo "---SIFO---" >> $LOG
echo -e "From revision:\t$CORE_LOCAL_REV"
echo -e "From revision:\t$CORE_LOCAL_REV" >> $LOG
echo -e "To revision:\t$CORE_REMOTE_REV"
echo -e "To revision:\t$CORE_REMOTE_REV" >> $LOG
echo -e "BRANCH: $INSTANCE_BRANCH"
echo -e "BRANCH: $INSTANCE_BRANCH" >> $LOG
echo "---$INSTANCE---"
echo "---$INSTANCE---" >> $LOG
echo -e "From revision:\t$INST_LOCAL_REV"
echo -e "From revision:\t$INST_LOCAL_REV" >> $LOG
echo -e "To revision:\t$INST_REMOTE_REV"
echo -e "To revision:\t$INST_REMOTE_REV" >> $LOG
echo -e "BRANCH: $INSTANCE_BRANCH"
echo -e "BRANCH: $INSTANCE_BRANCH" >> $LOG
echo "************************************************"
echo "************************************************" >> $LOG

if [ "$CORE_LOCAL_REV" == "$CORE_REMOTE_REV" ]
then
    echo -e "SIFO is already in the latest revision: $CORE_LOCAL_REV"
    echo -e "SIFO is already in the latest revision: $CORE_LOCAL_REV" >> $LOG
else
    cd $CORE
    git pull origin $SIFO_BRANCH >> $LOG
    # echo "What changed in SIFO..." >> $LOG
    # git whatchanged $CORE_LOCAL_REV..$CORE_REMOTE_REV >> $LOG
fi

if [ "$INST_REMOTE_REV" == "$INST_LOCAL_REV" ]
then
    echo -e "Instance $INSTANCE is already in the latest revision: $INST_REMOTE_REV"
    echo -e "Instance $INSTANCE is already in the latest revision: $INST_REMOTE_REV" >> $LOG
else
    cd $INSTANCEPATH
    git pull origin $INSTANCE_BRANCH >> $LOG
    echo "What changed in $INSTANCE..." >> $LOG
    git whatchanged $INST_LOCAL_REV..$INST_REMOTE_REV >> $LOG
fi

# Leaver user in the original path before executing this script
cd $ORIGINAL_PATH

# Append current log to accumulative changelog
cat $LOG >> $CHANGELOG
cat $LOG
