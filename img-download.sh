#!/bin/bash

# file="./downloads/current/xaa.txt"
# file="./downloads/current/xab.txt"
# file="./downloads/current/xac.txt"
# file="./downloads/current/xad.txt"
# file="./downloads/current/xae.txt"
# file="./downloads/current/xaf.txt"
# file="./downloads/current/xag.txt"
# file="./downloads/current/xah.txt"
# file="./downloads/current/xai.txt"
# file="./downloads/current/xaj.txt"
# file="./downloads/current/xak.txt"
# file="./downloads/current/xal.txt"
# file="./downloads/current/xam.txt"
# file="./downloads/current/xan.txt"
# file="./downloads/current/xao.txt"

file="./downloads/current/filesforupload.txt"

uploadPath="./uploads"
counter=0

while read line ; do
    IFS=";"
    set -- $line
    directory=$1
    filepath=$2
    fullpath="$uploadPath/$directory/$filepath"

    fulldirpath=${fullpath%/*}

    counter=$((counter+1))

    # if [ ! -d "$fulldirpath" ]
    # then
    #     mkdir -p "$fulldirpath"
    # fi

    if [ ! -f $fullpath ]
    then
        sleep 0.5s

        echo "$counter  wget --user=22477_xmlexport --password=MF1lHzTR --wait=1s -P $fulldirpath api2.gifts.ru/export/v2/catalogue/$filepath"
        wget --user=22477_xmlexport --password=MF1lHzTR --wait=1s -P $fulldirpath api2.gifts.ru/export/v2/catalogue/$filepath
    fi
done < $file
