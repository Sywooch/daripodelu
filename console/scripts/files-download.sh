#!/bin/bash


# ========================================================================
# логин
login="22477_xmlexport"

# пароль
pass="MF1lHzTR"

# Пауза в секундах между несколькими загрузками (в т.ч. повторами). Это снижает загруженность сервера.
# Чтобы указать значение в минутах, используйте "m", в часах - "h", в днях - "d" после числа.
wait="1s"

# путь к файлу со списком незагруженных картинок
file="./../../downloads/current/filesforupload.txt"

# путь к директории, в которую следует загружать картинки
uploadPath="./../../uploads"
# ========================================================================

# формирование файла со списком незагруженных картинок
php -c ~/etc/php.ini ./../../yii load/makefileslist

# проверка наличия файла со списком незагруженных картинок
if ! [ -f ./../../downloads/current/filesforupload.txt ];
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
        wget --user=$login --password=$pass --wait=$wait -P $fulldirpath api2.gifts.ru/export/v2/catalogue/$filepath
    fi
done < $file
