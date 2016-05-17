#!/bin/bash

create_archive() {
    if [ -z "$1" ]                          # Длина параметра #1 равна нулю?
    then
        echo "-Parameter #1 is zero length.-"
    fi

    if [ -z "$2" ]                          # Длина параметра #2 равна нулю?
    then
        echo "-Parameter #2 is zero length.-"
    fi

    src_folder="$1"
    dst_folder="$2"
    archive_name=archive_$(date +%Y-%m-%d_%H%M%S)

    if [ ! -d "$dst_folder" ]; then
        `mkdir "$dst_folder"`
    fi

    if [ "$(ls -A $src_folder)" ]; then
        `cd $src_folder ; zip -r $archive_name.zip . * ; mv $archive_name.zip $dst_folder ; `
    fi
}