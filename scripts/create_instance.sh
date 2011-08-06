#!/bin/sh
# Albert Lombarte y Sergio Ambel
#
if [ $# != 2 ]
# There must 2 argument
then
	echo ""
	echo "Usage: $0 instance_name domain_name.ext"
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
mkdir -p classes config controllers public/root public/static locale scripts models templates/_smarty/cache templates/_smarty/compile templates/_smarty/configs tests

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
echo "--> Changing permissions..."
chmod -R 777 templates/_smarty
chmod 777 config/configuration_files.config.php
chmod 777 config/templates.config.php
chmod 777 config/classes.config.php

cd ../..
echo ""
echo "Finished."
echo "1)  You can edit now instances/$1/config/domains.config.php"
echo "2)  Run http://$2/rebuild"
