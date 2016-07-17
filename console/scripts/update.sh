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

# вызов функции для создания архива в каталоге dst_folder
create_archive "$src_folder" "$dst_folder";
# если каталог src_folder не пустой, то очищаем его
if [ "$(ls -A $src_folder)" ]; then
    `rm $src_folder/*`
fi

# == создание бэкапа БД ================================================================================================

php -c ~/etc/php.ini $ROOT_PATH/yii tools/create-db-backup

# == загрузка xml-файлов, их парсинг и запись данных во временные таблицы ==============================================

# загрузка xml-файлов с сайта gifs.ru
# php -c ~/etc/php.ini $ROOT_PATH/yii load/downloadxml

wait="0.5s"

wget --user=$login --password=$pass -w $wait -P $src_folder api2.gifts.ru/export/v2/catalogue/tree.xml
sleep 0.5
wget --user=$login --password=$pass -w $wait -P $src_folder api2.gifts.ru/export/v2/catalogue/filters.xml
sleep 0.5
wget --user=$login --password=$pass -w $wait -P $src_folder api2.gifts.ru/export/v2/catalogue/product.xml
sleep 0.5
wget --user=$login --password=$pass -w $wait -P $src_folder api2.gifts.ru/export/v2/catalogue/stock.xml

# очистка временных таблиц БД
php -c ~/etc/php.ini $ROOT_PATH/yii load/droptables

# парсинг tree.xml и запись категорий во временную таблицу БД
php -c ~/etc/php.ini $ROOT_PATH/yii load/insertctg

# парсинг products.xml и запись товаров во временную таблицу БД
php -c ~/etc/php.ini $ROOT_PATH/yii load/insertprod

# парсинг products.xml и запись подчиненных товаров во временную таблицу БД
php -c ~/etc/php.ini $ROOT_PATH/yii load/insertslaveprod

# парсинг products.xml и запись доп. файлов товаров во временную таблицу БД
php -c ~/etc/php.ini $ROOT_PATH/yii load/insertattach

# парсинг products.xml и запись методов печати товаров во временную таблицу БД
php -c ~/etc/php.ini $ROOT_PATH/yii load/insertprint

# парсинг filters.xml и запись фильтров во временную таблицу БД
php -c ~/etc/php.ini $ROOT_PATH/yii load/insertfilters

# парсинг filters.xml и запись фильтров во временную таблицу БД
php -c ~/etc/php.ini $ROOT_PATH/yii load/insertprodfilters

# == загрузка изображений и файлов на хостинг ==========================================================================

# Пауза в секундах между несколькими загрузками (в т.ч. повторами). Это снижает загруженность сервера.
# Чтобы указать значение в минутах, используйте "m", в часах - "h", в днях - "d" после числа.
wait="0.5s"

# путь к директории, в которую следует загружать изображения и файлы
uploadPath="$ROOT_PATH/uploads"

# == загрузка изображений на хостинг ===================================================================================

# путь к файлу со списком незагруженных изображений
file="$ROOT_PATH/downloads/current/imagesforupload.txt"

# формирование файла со списком незагруженных изображений
php -c ~/etc/php.ini $ROOT_PATH/yii load/makeimglist

# проверка наличия файла со списком незагруженных изображений
if ! [ -f $ROOT_PATH/downloads/current/imagesforupload.txt ];
then
      echo "No file with images list for upload"
fi

counter=0
# загрузка изображений
while read line ; do
      IFS=";"
      set -- $line
      directory=$1
      filepath=$2
      fullpath="$uploadPath/$directory/$filepath"

      fulldirpath=${fullpath%/*}

      counter=$((counter+1))

      if [ ! -f $fullpath ]
      then
          sleep 0.5

          echo "$counter  wget --user=****** --password=****** --wait=$wait -P $fulldirpath api2.gifts.ru/export/v2/catalogue/$filepath"
          wget --user=$login --password=$pass -w $wait -P $fulldirpath api2.gifts.ru/export/v2/catalogue/$filepath
      fi
done < $file

# == загрузка файлов на хостинг ========================================================================================

# путь к файлу со списком незагруженных файлов
file="$ROOT_PATH/downloads/current/filesforupload.txt"

# формирование файла со списком незагруженных файлов
php -c ~/etc/php.ini $ROOT_PATH/yii load/makefileslist

# проверка наличия файла со списком незагруженных файлов
if ! [ -f $ROOT_PATH/downloads/current/filesforupload.txt ];
then
    echo "No file with images list for upload"
fi

counter=0
# загрузка файлов
while read line ; do
    IFS=";"
    set -- $line
    directory=$1
    filepath=$2
    fullpath="$uploadPath/$directory/$filepath"

    fulldirpath=${fullpath%/*}

    counter=$((counter+1))

    if [ ! -f $fullpath ]
    then
        sleep 0.5

        echo "$counter  wget --user=****** --password=****** --wait=$wait -P $fulldirpath api2.gifts.ru/export/v2/catalogue/$filepath"
        wget --user=$login --password=$pass --wait=$wait -P $fulldirpath api2.gifts.ru/export/v2/catalogue/$filepath
    fi
done < $file

# == создание миниатюр =================================================================================================

# создание миниатюр из фотографий товаров
php -c ~/etc/php.ini $ROOT_PATH/yii tools/createthumbs

# == запись данных в "основные" таблицы ================================================================================

# добавление новых категорий в "основную" таблицу категорий в БД
php -c ~/etc/php.ini $ROOT_PATH/yii update/categories

# добавление новых типов фильтров в "основную" таблицу в БД
php -c ~/etc/php.ini $ROOT_PATH/yii update/filter-types

# добавление новых фильтров в "основную" таблицу фильтров в БД
php -c ~/etc/php.ini $ROOT_PATH/yii update/filters

# добавление новых методов печати в "основную" таблицу в БД
php -c ~/etc/php.ini $ROOT_PATH/yii update/prints

# добавление новых товаров в "основную" таблицу товаров в БД
php -c ~/etc/php.ini $ROOT_PATH/yii update/products

# добавление новых "подчиненных товаров" в "основную" таблицу в БД
php -c ~/etc/php.ini $ROOT_PATH/yii update/slave-products

# добавление новых дополнительных файлов в "основную" таблицу в БД
php -c ~/etc/php.ini $ROOT_PATH/yii update/product-attachments

# добавление новых связей "метод нанесения - товар" в "основную" таблицу в БД
php -c ~/etc/php.ini $ROOT_PATH/yii update/print-product-rel

# добавление новых связей "фильтр - продукт" в "основную" таблицу в БД
php -c ~/etc/php.ini $ROOT_PATH/yii update/product-filter-rel

# вызов функции для создания архива в каталоге dst_folder
create_archive "$src_folder" "$dst_folder";
# если каталог src_folder не пустой, то очищаем его
if [ "$(ls -A $src_folder)" ]; then
    `rm $src_folder/*`
fi
