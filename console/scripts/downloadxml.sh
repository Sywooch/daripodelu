#!/bin/bash

# абсолютный путь к папке, где расположен запускаемый скрипт
# SCRIPT_PATH=`pwd`/`dirname "$0"`
SCRIPT_PATH="$HOME/xn--80ahbenushh0b.xn--p1ai/docs/console/scripts"

# абсолютный путь к корневой папке сайта
ROOT_PATH="$SCRIPT_PATH/../.."

# подключение файла с настройками
source "$ROOT_PATH/console/config/config.sh"
# подключение библиотеки со вспомогательными функциями
source "$SCRIPT_PATH/lib.sh"

# абослютный путь к папке с текущими xml-файлами
src_folder="$ROOT_PATH/downloads/current"
# абсолютный путь к папке с архивами
dst_folder="$ROOT_PATH/downloads/archive"

wait="0.5s"

wget --user=$login --password=$pass -w $wait -P $src_folder api2.gifts.ru/export/v2/catalogue/tree.xml
sleep 0.5
wget --user=$login --password=$pass -w $wait -P $src_folder api2.gifts.ru/export/v2/catalogue/filters.xml
sleep 0.5
wget --user=$login --password=$pass -w $wait -P $src_folder api2.gifts.ru/export/v2/catalogue/product.xml
sleep 0.5
wget --user=$login --password=$pass -w $wait -P $src_folder api2.gifts.ru/export/v2/catalogue/stock.xml
