#!/bin/bash

SCRIPT_NAME=`pwd`/`dirname "$0"`
ROOT_PATH="$SCRIPT_NAME/../.."

source "$SCRIPT_NAME/lib.sh"

src_folder="$ROOT_PATH/downloads/current"
dst_folder="$ROOT_PATH/downloads/archive"

create_archive "$src_folder" "$dst_folder" ;
if [ "$(ls -A $src_folder)" ]; then
    `rm $src_folder/*`
fi
