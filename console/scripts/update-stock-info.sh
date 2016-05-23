#!/bin/bash

SCRIPT_PATH=`pwd`/`dirname "$0"`
ROOT_PATH="$SCRIPT_PATH/../.."

source "$SCRIPT_PATH/lib.sh"

src_folder="$ROOT_PATH/downloads/current"
dst_folder="$ROOT_PATH/downloads/archive"

create_archive "$src_folder" "$dst_folder";
if [ "$(ls -A $src_folder)" ]; then
    `rm $src_folder/*`
fi

# загрузка xml-файлов с сайта gifs.ru
php -c ~/etc/php.ini $ROOT_PATH/yii load/downloadstock
php -c ~/etc/php.ini $ROOT_PATH/yii update/stock

create_archive "$src_folder" "$dst_folder" "stock";
if [ "$(ls -A $src_folder)" ]; then
    `rm $src_folder/*`
fi