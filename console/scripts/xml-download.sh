#!/bin/bash

SCRIPT_PATH=`pwd`/`dirname "$0"`
ROOT_PATH="$SCRIPT_PATH/../.."

# архивация старых xml-файлов
source "$SCRIPT_PATH/lib.sh"

# путь каталогам, где храняться xml-файлы для архивирования (каталог src_folder)
# и архивы (каталог dst_folder)
src_folder="$ROOT_PATH/downloads/current"
dst_folder="$ROOT_PATH/downloads/archive"

# вызов функции для создания архива в каталоге dst_folder
create_archive "$src_folder" "$dst_folder" ;

# если каталог src_folder не пустой, то очищаем его
if [ "$(ls -A $src_folder)" ]; then
    `rm $src_folder/*`
fi


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


# вызов функции для создания архива в каталоге dst_folder
create_archive "$src_folder" "$dst_folder" ;

# если каталог src_folder не пустой, то очищаем его
if [ "$(ls -A $src_folder)" ]; then
    `rm $src_folder/*`
fi