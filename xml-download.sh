#!/bin/bash

# очистка таблиц БД
./yii load/droptables

# загрузка xml-файлов с сайта gifs.ru
./yii load/downloadxml

# анализ tree.xml и запись категорий в БД
./yii load/insertctg

# анализ products.xml и запись товаров в БД
./yii load/insertprod

# анализ products.xml и запись подчиненных товаров в БД
./yii load/insertslaveprod

# анализ products.xml и запись доп. файлов товаров в БД
./yii load/insertattach

# анализ products.xml и запись методов печати товаров в БД
./yii load/insertprint

# анализ filters.xml и запись фильтров в БД
./yii load/insertfilters

# анализ filters.xml и запись фильтров в БД
./yii load/insertprodfilters
