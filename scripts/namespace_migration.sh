#!/bin/bash
# Script to migrate your instance to namespaces
# Albert Lombarte

# Basic parameters check
if [ $# != 2 ]
then
	echo "USAGE: $0 </sifo/install/dir> <instance-name>"
	echo "--This script adapt your PHP 5.2 instance to a PHP 5.3 using namespaces."
	echo ""
	echo "Example: $0 /var/www/sifo_git sifoweb"
	exit 0
fi

# Remove trailing slash
SIFO_PATH=`echo $1 | sed 's/[/]*$//'`
INSTANCE=$2
INSTANCE_DIR="$SIFO_PATH/instances/$INSTANCE"

NAMESPACE=`echo $INSTANCE| perl -ne 'print ucfirst($_)'`

if [ ! -d "$INSTANCE_DIR" ]; then
	echo "ERROR: The parameters you passed didn't work, this is not a valid dir:"
	echo $INSTANCE_DIR
	exit 0
fi

clear
echo ''
echo "Your instance is going to be moved from PHP 5.2 to PHP 5.3 using namespaces."
echo "The latest SIFO code found in GitHub should exist."
echo "Additional work might be needed to entirely adapt your code. You better have this code versioned :)"
echo '----------------------------------------'
echo "Directory: $INSTANCE_DIR"
echo "Namespace: $NAMESPACE"
echo "Instance:  $NAMESPACE"
echo '----------------------------------------'
echo 'Press a key to continue in the party or CTRL-C to leave it'
echo ''
echo ''
read

echo "OH MY GOD! You did it!"

# PARTY BEGINS HERE:
mkdir -p $INSTANCE_DIR/public/static/{js,css}/generated/
chmod -R 777 $INSTANCE_DIR/public/static/{js,css}/generated/

echo "-- The 'default' instance has been renamed to 'common', fixing..."
find $INSTANCE_DIR/ -type f -name "*.php" -exec sed -i 's/instances\/default/instances\/common/g' {} \;
echo "[IMPORTANT] Manually change in your domains.config.php the inheritance from 'default' to 'common'"
echo "-- Getting rid of old naming SEOframework, long life Sifo..."
find $INSTANCE_DIR/config/ -type f -name "*.php" -exec sed -i 's/SEOframework/Sifo/g' {} \;

echo "Moving Smarty references of 3.0.7 to 3.1.4"
find $INSTANCE_DIR/ -type f -name "*.php" -exec sed -i 's/Smarty-3.0.7/Smarty-3.1.4/g' {} \;

echo "-- Renaming class UrlParser to Urls"
find $INSTANCE_DIR/ -type f -name "*.php" -exec sed -i 's/UrlParser/Urls/g' {} \;

# Correct getClass usage
#echo "-- Moving from getClass to 'new'"
#find $INSTANCE_DIR/ -type f -name "*.php" -exec sed -i 's/\$this->getClass(\s*["]\(\w*\)["]\(,*\s*\)\(true\)*\s*[)]/new \1()/g' {} \;
#find $INSTANCE_DIR/ -type f -name "*.php" -exec sed -i "s/\$this->getClass(\s*[']\(\w*\)[']\(,*\s*\)\(true\)*\s*[)]/new \1()/g" {} \;

CLASSES=(
'CLBootstrap'
'Bootstrap'
'Benchmark'
'Cache'
'CacheDisk'
'Client'
'Config'
'Exception_Configuration'
'Controller'
'Cookie'
'FilterCookieRuntime'
'Crypt'
'CssPacker'
'Database'
'LoadBalancer_ADODB'
'Dir'
'DirectoryList'
'FilterFilesByExtensionIterator'
'Domains'
'SEO_Exception'
'Exception_301'
'Exception_302'
'Exception_303'
'Exception_304'
'Exception_307'
'Exception_400'
'Exception_401'
'Exception_403'
'Exception_404'
'Exception_405'
'Exception_500'
'Exception_503'
'Exception_100'
'Exception_101'
'Exception_201'
'Exception_202'
'Exception_203'
'Exception_204'
'Exception_205'
'Exception_206'
'Exception_300'
'Exception_305'
'Exception_402'
'Exception_406'
'Exception_407'
'Exception_408'
'Exception_409'
'Exception_410'
'Exception_411'
'Exception_412'
'Exception_413'
'Exception_414'
'Exception_415'
'Exception_416'
'Exception_417'
'Exception_501'
'Exception_502'
'Exception_504'
'Exception_505'
'FilterPost'
'FilterPost'
'FilterGet'
'FilterRequest'
'FilterServer'
'FilterCustom'
'FilterCookie'
'FilterSession'
'FilterFiles'
'FilterEnv'
'FilterException'
'FlashMessages'
'Form'
'Exception_Form'
'I18N'
'Images'
'ImageController'
'JsPacker'
'LoadBalancer'
'Mail'
'MediaGenerator'
'MediaPacker'
'Metadata'
'Model'
'Mysql'
'MysqlModel'
'MysqlStatement'
'MysqlDebug'
'MysqlDebugStatement'
'RedisModel'
'Registry'
'Exception_Registry'
'Router'
'Search'
'Session'
'Urls'
'View'
'YouTube'
'Twitter'
)

 NUM_ELEMENTS=${#CLASSES[@]}
 echo "-- Renaming Class::method() to \\Sifo\\Class:method() (total: $NUM_ELEMENTS class types)"
 for (( i=0;i<$NUM_ELEMENTS;i++)); do
     CLASS=${CLASSES[${i}]}
 	echo "---- Applying namespaces to $CLASS"
 	
 	# tab + class + parenthesis or :: or tab
 	CLASS_USE_EXPR='s/\([\t\=]\)\('$CLASS'\)\([\t(:;\{]\)/\1\\Sifo\\\2\3/g'
 	# When the class name is in the end of the line (like "extends Controller")
 	CLASS_END_EXPR='s/\([\t\=]\)\('$CLASS'\)$/\1\\Sifo\\\2/g'
	CLASS_INI_EXPR='s/^\('$CLASS'\)\([\t(:;\{]\)/\\Sifo\\\1\2/g'
 	
 	find $INSTANCE_DIR/ -type f -name "*.php" -exec sed -i $CLASS_USE_EXPR {} \;
 	find $INSTANCE_DIR/ -type f -name "*.php" -exec sed -i $CLASS_END_EXPR {} \;
	find $INSTANCE_DIR/ -type f -name "*.php" -exec sed -i $CLASS_INI_EXPR {} \;
 	
 	# Repeat the same with spaces. The \s or \  operator does not work properly in old sed versions. 
 	CLASS_USE_SPACE_EXPR='s/\([[:space:]]\)\('$CLASS'\)\([\t(:;\{]\)/\1\\Sifo\\\2\3/g'
	CLASS_END_SPACE_EXPR='s/\([[:space:]]\)\('$CLASS'\)$/\1\\Sifo\\\2/g'
 	find $INSTANCE_DIR/ -type f -name "*.php" -exec sed -i $CLASS_USE_SPACE_EXPR {} \;
	find $INSTANCE_DIR/ -type f -name "*.php" -exec sed -i $CLASS_END_SPACE_EXPR {} \;
	
	# BUG: Use of class like Config :: getInstance(), note spaces, won't be replaced
 	
 done

echo "-- Removing your instance name from class names (e.g: HomeIndex$NAMESPACE\Controller to HomeIndexController)"
INSTANCE_CLASS_EXPR='s/\('$NAMESPACE'\)\(Controller\|Model\)/\2/g'
find $INSTANCE_DIR/ -type f -name "*.php" -exec sed -i $INSTANCE_CLASS_EXPR {} \;
INSTANCE_CLASS_EXPR='s/class\('$NAMESPACE'\)/class\1/g'
find $INSTANCE_DIR/classes/ -type f -name "*.php" -exec sed -i $INSTANCE_CLASS_EXPR {} \;

# Add namespace
echo "-- Adding namespace $NAMESPACE to your files (as the second line)"
find $INSTANCE_DIR/{controllers,models,classes} -type f -name "*.php" -exec sed -i "2i\
namespace $NAMESPACE;" {} \;

echo "-- Copying the new classes.config.php file format You should overwrite them and then rebuild."
cp $SIFO_PATH/instances/common/config/classes.config.php $INSTANCE_DIR/config/classes.config.php
cp $SIFO_PATH/instances/common/config/templates.config.php $INSTANCE_DIR/config/templates.config.php

echo "-- Cleaning Smarty compiled files"
rm -fr $INSTANCE_DIR/templates/_smarty/compile/*


echo "-- Populating external Github Submodules"
echo "---- Downloading Facebook libraries"
cd $SIFO_PATH/libs/Facebook-php-sdk
git submodule init
git submodule update

echo "---- Downloading Predis libraries"
cd $SIFO_PATH/libs/Predis
git submodule init
git submodule update


echo "Script Finished."
echo "Now go to /rebuild and then start testing"


