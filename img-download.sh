#!/bin/bash

file="./downloads/current/filesforupload_min.txt"

uploadPath="./uploads"

while read line ; do
    IFS=";"
    set -- $line
    directory=$1
    filepath=$2
    fullpath="$uploadPath/$directory/$filepath"

    fulldirpath=${fullpath%/*}

    # if [ ! -d "$fulldirpath" ]
    # then
    #     mkdir -p "$fulldirpath"
    # fi

    if [ ! -f $fullpath ]
    then
        sleep 1s

        echo "wget --user=22477_xmlexport --password=MF1lHzTR --wait=1s -P $fulldirpath api2.gifts.ru/export/v2/catalogue/$filepath"
        wget --user=22477_xmlexport --password=MF1lHzTR --wait=1s -P $fulldirpath api2.gifts.ru/export/v2/catalogue/$filepath
    fi
done < $file
