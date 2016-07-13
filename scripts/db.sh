#!/bin/bash

db_user='root'
db_pwd='mysql'

#if [ ! -z $1 ] && [ $1 == "reload" ]; then
mysql -u $db_user -p$db_pwd < db/cities_states.sql
mysql -u $db_user -p$db_pwd < db/mysql.sql
echo "Database load/reload"
#fi
