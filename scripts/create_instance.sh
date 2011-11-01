#!/bin/sh
# Albert Lombarte, Sergio Ambel
#
if [ $# != 2 ]
# There must be 2 arguments
then
	echo ""
	echo "Usage: $0 <instance_name> <yourdomain.local>"
	echo "Instance creation script. Prepares the directory tree and necessary files to run your new instance."
	echo "You can test this script as many times as you need. To revert the changes you only need to delete"
	echo "the directory that will be created under installdir/instances/<instance_name>"
	echo ""
	echo "    Parameters:"
	echo "    -----------"
	echo "    <instance_name>: Lowercase, letters only. The name you want to put to your instance and to your PHP namespace."
	echo "    <yourdomain>: The domain you are going to use to access this page. Example: mysite.com or mysite.local (devel)"
	echo ""
	echo "    Examples:"
	echo "    ---------"
	echo "    ./create_instance.sh coconut my-coconut-best-recipes.com"
	echo "    or..."
	echo "    ./create_instance.sh coconut my-coconut-best-recipes.local"
	echo ""
	echo "--"
    exit 1
fi
clear
echo "An instance named '$1' with '$2' domain name will be created. Press ENTER to continue or CTRL-C to abort."
read keypress
cd ../instances
echo ""
echo "--> Creating tree..."
mkdir $1
cd $1
mkdir -p classes config controllers public/root public/static/js/generated public/static/css/generated locale scripts models templates/_smarty/cache templates/_smarty/compile templates/_smarty/configs tests

echo "--> Creating test structure..."
absolute_dir=`pwd`
ln -s $absolute_dir/tests/ ../../tests/instances/$1

echo "--> Copying the config files..."
# cp -R ../default/config/* config
rsync -aC ../default/config .

# Customizing the configuration_files.config.php and domains.config.php to the instance:
echo "--> Customizing config files with the instance name..."
cat ../default/public/root/index.php | sed -e "s/default/$1/g" > ../$1/public/root/index.php
cat ../default/config/configuration_files.config.php | sed -e "s/default/$1/g" > ../$1/config/configuration_files.config.php
cat ../default/config/domains.config.php | sed -e "s/default/$1/g" | sed -e "s/seoframework.local/$2/g" > ../$1/config/domains.config.php

#
echo "--> Creating the tipically messages files..."
# Messages files cannot be empty
echo "<?php \$translations[\"hello\"] = '';" > locale/messages_en_US.php

# Create the customized index.php:
echo "--> Creating a index.php for this $1 instance..."
cat ../default/public/root/index.php | sed -e "s/default/$1/g" > ../$1/public/root/index.php

# Deleting all the .svn info probably copied by error:
echo "--> Delete some .svn directory probably copied in error..."
find . -name ".svn" -exec rm -fr \{\} \;

# Permissions:
echo "--> Make needed folders writable by everyone..."
chmod -R 777 templates/_smarty
chmod -R 777 public/static/js/generated
chmod -R 777 public/static/css/generated
chmod 777 config/configuration_files.config.php
chmod 777 config/templates.config.php
chmod 777 config/classes.config.php
echo "(you should apply proper permissions according to your users later)"

cd ../..
echo ""
echo "Finished."
echo "1)  You can edit now instances/$1/config/domains.config.php"
echo "2)  Run http://$2/rebuild"
