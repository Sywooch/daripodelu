#! /bin/sh

file="./downloads/current/filesforupload_min.txt"

uploadPath="./uploads"

while read line ; do
    IFS=";"
    set -- $line
    directory=$1
    filepath=$2
    fullpath="$uploadPath/$directory/$filepath"

    fulldirpath=${fullpath%/*}

    if [ ! -d "$fulldirpath" ]
    then
        mkdir -p "$fulldirpath"
    fi
done < $file
