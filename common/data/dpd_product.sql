-- phpMyAdmin SQL Dump
-- version 4.4.10
-- http://www.phpmyadmin.net
--
-- Хост: localhost:3306
-- Время создания: Дек 16 2015 г., 22:14
-- Версия сервера: 5.5.42
-- Версия PHP: 5.4.42

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `daripodelu_db`
--

-- --------------------------------------------------------

--
-- Структура таблицы `dpd_product`
--

CREATE TABLE `dpd_product` (
  `id` int(11) NOT NULL COMMENT 'ID товара',
  `catalogue_id` int(11) NOT NULL COMMENT 'ID категории',
  `group_id` int(11) DEFAULT NULL COMMENT 'ID группы',
  `code` varchar(100) DEFAULT NULL COMMENT 'Артикул',
  `name` varchar(255) NOT NULL COMMENT 'Название',
  `product_size` varchar(255) NOT NULL COMMENT 'Размер',
  `matherial` varchar(255) NOT NULL COMMENT 'Материал',
  `small_image` varchar(255) DEFAULT NULL COMMENT 'Путь к файлу картинки 200х200',
  `big_image` varchar(255) DEFAULT NULL COMMENT 'Путь к файлу картинки 280х280',
  `super_big_image` varchar(255) DEFAULT NULL COMMENT 'Путь к файлу картинки 1000х1000',
  `content` text COMMENT 'Описание',
  `status_id` int(1) DEFAULT NULL COMMENT 'ID статуса',
  `status_caption` varchar(40) NOT NULL COMMENT 'Статус',
  `brand` varchar(60) NOT NULL COMMENT 'Бренд',
  `weight` float(9,2) NOT NULL COMMENT 'Вес',
  `pack_amount` int(11) DEFAULT NULL COMMENT 'Количество в упаковке',
  `pack_weigh` float(9,2) DEFAULT NULL COMMENT 'Вес упаковки',
  `pack_volume` float(9,2) DEFAULT NULL COMMENT 'Объем упаковки',
  `pack_sizex` float(8,1) DEFAULT NULL COMMENT 'Ширина упаковки',
  `pack_sizey` float(8,1) DEFAULT NULL COMMENT 'Высота упаковки',
  `pack_sizez` float(8,1) DEFAULT NULL COMMENT 'Глубина упаковки',
  `amount` int(11) NOT NULL DEFAULT '0' COMMENT 'Всего на складе',
  `free` int(11) NOT NULL DEFAULT '0' COMMENT 'Доступно для резервирования',
  `inwayamount` int(11) NOT NULL DEFAULT '0' COMMENT 'Всего в пути (поставка)',
  `inwayfree` int(11) NOT NULL DEFAULT '0' COMMENT 'Доступно для резервирования из поставки',
  `enduserprice` float(14,2) NOT NULL DEFAULT '0.00' COMMENT 'Цена End-User',
  `user_row` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Создан пользователем'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `dpd_product`
--
ALTER TABLE `dpd_product`
  ADD PRIMARY KEY (`id`),
  ADD KEY `dpd_product_fk0` (`catalogue_id`);

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `dpd_product`
--
ALTER TABLE `dpd_product`
  ADD CONSTRAINT `dpd_product_fk0` FOREIGN KEY (`catalogue_id`) REFERENCES `dpd_catalogue` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
