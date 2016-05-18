#!/bin/bash

SCRIPT_PATH=`pwd`/`dirname "$0"`
ROOT_PATH="$SCRIPT_PATH/../.."

# file="./../../downloads/current/xaa"
# file="./../../downloads/current/xab"
# file="./../../downloads/current/xac"
# file="./../../downloads/current/xad"
# file="./../../downloads/current/xae"
# file="./../../downloads/current/xaf"
# file="./../../downloads/current/xag"
# file="./../../downloads/current/xah"
# file="./../../downloads/current/xai"
# file="./../../downloads/current/xaj"
# file="./../../downloads/current/xak"
# file="./../../downloads/current/xal"
# file="./../../downloads/current/xam"
# file="./../../downloads/current/xan"
# file="./../../downloads/current/xao"


# ========================================================================
# логин
login="22477_xmlexport"

# пароль
pass="MF1lHzTR"

# Пауза в секундах между несколькими загрузками (в т.ч. повторами). Это снижает загруженность сервера.
# Чтобы указать значение в минутах, используйте "m", в часах - "h", в днях - "d" после числа.
wait="0.5s"

# путь к файлу со списком незагруженных картинок
file="$ROOT_PATH/downloads/current/imagesforupload.txt"

# путь к директории, в которую следует загружать картинки
uploadPath="$ROOT_PATH/uploads"
# ========================================================================

# формирование файла со списком незагруженных картинок
php -c ~/etc/php.ini $ROOT_PATH/yii load/makeimglist

# проверка наличия файла со списком незагруженных картинок
if ! [ -f $ROOT_PATH/downloads/current/imagesforupload.txt ];
then
      echo "No file with images list for upload"
fi

counter=0

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
