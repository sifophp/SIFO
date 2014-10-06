#!/bin/bash
# Albert Lombarte
# alombarte@gmail.com
# This script updates the SIFO code to the latest and the passed instance as well.
# It is meant to update your code in production.

USER=`whoami`
TODAY=$(date "+%Y-%b-%d")
TIME=$(date "+%k:%M")


ORIGINAL_PATH=$PWD
SCRIPTPATH=$(cd ${0%/*} && echo $PWD/${0##*/})
APP_BASE_DIR=`dirname "$SCRIPTPATH"`
APP_BASE_DIR=`cd "${APP_BASE_DIR}/../../../../.." && pwd -P`

if [ $# != 1 ]
then
    echo ""
    echo ""
	echo "This script puts '$APP_BASE_DIR' to its latest version in the current branch"
	echo "--USAGE: $0 <instancename>"
	echo ""
	exit 0
fi

INSTANCE=$1
INSTANCEPATH="$APP_BASE_DIR/instances/$INSTANCE"
LOG_PATH="$INSTANCEPATH/logs/deploys"
LOG="$LOG_PATH/deploy_$TODAY.log"


#if [ "$USER" == "root" ]
#then
#        clear
#        echo -e "Do not use root for updating code"
#        exit 0
#fi

mkdir -p $APP_BASE_DIR/instances/$INSTANCE/logs/
mkdir -p $LOG_PATH

echo "" &&
echo "***********************************************" &&
echo "***************** D E P L O Y *****************" &&
echo "***********************************************" &&
echo "$INSTANCE update: $TODAY at $TIME ($USER)" &&
echo "***********************************************" &&
echo "" &&
echo "Updating servers..." | tee -a $LOG


cd $APP_BASE_DIR
APP_BRANCH=`git branch | awk '/^\*/ { print $2 }'`
APP_BASE_DIR_REMOTE_REV=`git ls-remote origin $APP_BRANCH | sed 's/\([0-9a-f]\{10\}\)\(.*\)/\1/g' | head -n 1`
APP_BASE_DIR_LOCAL_REV=`git rev-parse refs/heads/$APP_BRANCH | sed 's/\([0-9a-f]\{10\}\)\(.*\)/\1/g'`

cd $INSTANCEPATH
INSTANCE_BRANCH=`git branch | awk '/^\*/ { print $2 }'`
INST_REMOTE_REV=`git ls-remote origin $INSTANCE_BRANCH | sed 's/\([0-9a-f]\{10\}\)\(.*\)/\1/g' | head -n 1`
INST_LOCAL_REV=`git rev-parse refs/heads/$INSTANCE_BRANCH | sed 's/\([0-9a-f]\{10\}\)\(.*\)/\1/g'`

echo "---APP---" &&
echo -e "From revision:\t$APP_BASE_DIR_LOCAL_REV" &&
echo -e "To revision:\t$APP_BASE_DIR_REMOTE_REV"  &&
echo "BRANCH: $APP_BRANCH" &&
echo "---$INSTANCE---" &&
echo -e "From revision:\t$INST_LOCAL_REV" &&
echo -e "To revision:\t$INST_REMOTE_REV" &&
echo "BRANCH: $INSTANCE_BRANCH" &&
echo "************************************************" | tee -a $LOG

if [ "$APP_BASE_DIR_LOCAL_REV" == "$APP_BASE_DIR_REMOTE_REV" ]
then
    echo "Your app is already in the latest revision: $APP_BASE_DIR_LOCAL_REV" | tee -a $LOG
else
    cd $APP_BASE_DIR
    git pull origin $APP_BRANCH 2>&1 | tee -a $LOG
    # echo "What changed in SIFO..." | tee -a $LOG
    # git whatchanged $APP_BASE_DIR_LOCAL_REV..$APP_BASE_DIR_REMOTE_REV | tee -a $LOG
fi

if [ "$INST_REMOTE_REV" == "$INST_LOCAL_REV" ]
then
    echo "Instance $INSTANCE is already in the latest revision: $INST_REMOTE_REV" | tee -a $LOG
else
    cd $INSTANCEPATH
    git pull origin $INSTANCE_BRANCH | tee -a $LOG
    echo "What changed in $INSTANCE..." | tee -a $LOG
    git whatchanged $INST_LOCAL_REV..$INST_REMOTE_REV | tee -a $LOG
fi

echo "Installing your composer.lock dependencies" | tee -a $LOG
composer install | tee -a $LOG

# Leaver user in the original path before executing this script
cd $ORIGINAL_PATH