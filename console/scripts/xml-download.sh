#!/bin/bash

SCRIPT_PATH=`pwd`/`dirname "$0"`
ROOT_PATH="$SCRIPT_PATH/../.."

# загрузка xml-файлов с сайта gifs.ru
php -c ~/etc/php.ini $ROOT_PATH/yii load/downloadxml

# очистка таблиц БД
php -c ~/etc/php.ini $ROOT_PATH/yii load/droptables

# анализ tree.xml и запись категорий в БД
php -c ~/etc/php.ini $ROOT_PATH/yii load/insertctg

# анализ products.xml и запись товаров в БД
php -c ~/etc/php.ini $ROOT_PATH/yii load/insertprod

# анализ products.xml и запись подчиненных товаров в БД
php -c ~/etc/php.ini $ROOT_PATH/yii load/insertslaveprod

# анализ products.xml и запись доп. файлов товаров в БД
php -c ~/etc/php.ini $ROOT_PATH/yii load/insertattach

# анализ products.xml и запись методов печати товаров в БД
php -c ~/etc/php.ini $ROOT_PATH/yii load/insertprint

# анализ filters.xml и запись фильтров в БД
php -c ~/etc/php.ini $ROOT_PATH/yii load/insertfilters

# анализ filters.xml и запись фильтров в БД
php -c ~/etc/php.ini $ROOT_PATH/yii load/insertprodfilters
