-- phpMyAdmin SQL Dump
-- version 4.4.10
-- http://www.phpmyadmin.net
--
-- Хост: localhost:3306
-- Время создания: Дек 14 2015 г., 00:00
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
-- Структура таблицы `dpd_catalogue`
--

CREATE TABLE `dpd_catalogue` (
  `id` int(11) NOT NULL COMMENT 'ID категории',
  `parent_id` int(11) NOT NULL DEFAULT '0' COMMENT 'ID родительской категории',
  `name` varchar(255) NOT NULL COMMENT 'Название',
  `uri` varchar(255) NOT NULL COMMENT 'URI'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `dpd_config`
--

CREATE TABLE `dpd_config` (
  `id` int(10) unsigned NOT NULL COMMENT 'ID',
  `param` varchar(128) NOT NULL COMMENT 'Имя переменной',
  `value` text NOT NULL COMMENT 'Значение',
  `default` text NOT NULL COMMENT 'По умолчанию',
  `label` varchar(255) NOT NULL COMMENT 'Название',
  `type` varchar(64) NOT NULL COMMENT 'Тип'
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `dpd_config`
--

INSERT INTO `dpd_config` (`id`, `param`, `value`, `default`, `label`, `type`) VALUES
(1, 'SITE_NAME', 'Дариподелу', 'Сайт', 'Название сайта', 'string'),
(2, 'SITE_ADMIN_EMAIL', 'ratmir85@gmail.com', 'ratmir85@gmail.com', 'E-mail администратора', 'string'),
(3, 'SITE_META_DESCRIPT', '', '', 'Meta Description', 'string'),
(4, 'SITE_META_KEYWORDS', '', '', 'Meta Keywords', 'string'),
(5, 'NEWS_ITEMS_PER_PAGE', '12', '8', 'Количество новостей на странице', 'integer'),
(6, 'NEWS_ITEMS_PER_HOME', '3', '3', 'Количество новостей на Главной странице', 'integer'),
(7, 'GATE_LOGIN', '22477_xmlexport', '', 'Логин от Gifts.ru', 'string'),
(8, 'GATE_PASSWORD', 'MF1lHzTR', '', 'Пароль от Gifts.ru', 'string');

-- --------------------------------------------------------

--
-- Структура таблицы `dpd_filter`
--

CREATE TABLE `dpd_filter` (
  `id` int(11) NOT NULL COMMENT 'ID фильтра',
  `name` varchar(30) NOT NULL COMMENT 'Название',
  `type_id` int(11) NOT NULL COMMENT 'ID типа фильтра'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `dpd_filter_type`
--

CREATE TABLE `dpd_filter_type` (
  `id` int(11) NOT NULL COMMENT 'ID типа фильтра',
  `name` varchar(30) NOT NULL COMMENT 'Название типа'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `dpd_image`
--

CREATE TABLE `dpd_image` (
  `id` int(10) unsigned NOT NULL COMMENT 'ID',
  `model` varchar(25) NOT NULL COMMENT 'Название модели',
  `ctg_id` int(11) DEFAULT NULL COMMENT 'ID категории',
  `owner_id` int(10) unsigned NOT NULL COMMENT 'ID владельца',
  `file_name` varchar(100) NOT NULL COMMENT 'Имя файла',
  `title` varchar(100) DEFAULT NULL COMMENT 'Заголовок файла',
  `description` varchar(255) DEFAULT NULL COMMENT 'Описание файла',
  `is_main` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT 'Главное фото',
  `weight` int(11) NOT NULL COMMENT 'Приоритет',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT 'Статус'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `dpd_menu_tree`
--

CREATE TABLE `dpd_menu_tree` (
  `id` int(11) NOT NULL COMMENT 'ID',
  `lft` int(11) NOT NULL,
  `rgt` int(11) NOT NULL,
  `depth` int(11) NOT NULL COMMENT 'Уровень вложенности',
  `parent_id` int(11) NOT NULL COMMENT 'ID родительского узла',
  `name` varchar(255) NOT NULL COMMENT 'Название',
  `alias` varchar(70) NOT NULL COMMENT 'Псевдоним',
  `module_id` varchar(40) DEFAULT NULL COMMENT 'Модуль',
  `controller_id` varchar(40) NOT NULL COMMENT 'Контроллер',
  `action_id` varchar(40) NOT NULL COMMENT 'Действие',
  `ctg_id` int(10) unsigned DEFAULT NULL COMMENT 'Категория',
  `item_id` int(10) unsigned DEFAULT NULL COMMENT 'Элемент',
  `can_be_parent` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Может иметь дочерние узлы',
  `show_in_menu` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Может быть пунктом меню',
  `show_as_link` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT 'Отображать как ссылку',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT 'Статус'
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `dpd_menu_tree`
--

INSERT INTO `dpd_menu_tree` (`id`, `lft`, `rgt`, `depth`, `parent_id`, `name`, `alias`, `module_id`, `controller_id`, `action_id`, `ctg_id`, `item_id`, `can_be_parent`, `show_in_menu`, `show_as_link`, `status`) VALUES
(1, 1, 18, 0, 0, 'Корень сайта', '_root', NULL, 'controller', 'action', NULL, NULL, 1, 1, 1, 1),
(2, 2, 3, 1, 1, 'Каталог продукции', 'catalogue', NULL, 'catalogue', 'index', NULL, NULL, 1, 0, 1, 1),
(3, 4, 15, 1, 1, 'Методы нанесения', 'coating-methods', NULL, 'page', 'view', NULL, 1, 1, 1, 1, 1),
(4, 16, 17, 1, 1, '«Дари по делу»', 'dari-po-delu', NULL, 'page', 'view', NULL, 2, 1, 1, 1, 1),
(5, 5, 6, 2, 3, 'Шелкография', 'shelkografia', NULL, 'page', 'view', NULL, 3, 0, 1, 1, 1),
(6, 7, 8, 2, 3, 'Флекс', 'fleks', NULL, 'page', 'view', NULL, 4, 0, 1, 1, 1),
(7, 9, 10, 2, 3, 'Вышивка', 'vsivka', NULL, 'page', 'view', NULL, 5, 0, 1, 1, 1),
(8, 11, 12, 2, 3, 'Тампопечать', 'tampopecat', NULL, 'page', 'view', NULL, 6, 0, 1, 1, 1),
(9, 13, 14, 2, 3, 'УФ-печать', 'uf-pecat', NULL, 'page', 'view', NULL, 7, 0, 1, 1, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `dpd_migration`
--

CREATE TABLE `dpd_migration` (
  `version` varchar(180) NOT NULL,
  `apply_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `dpd_migration`
--

INSERT INTO `dpd_migration` (`version`, `apply_time`) VALUES
('m000000_000000_base', 1446809297),
('m130524_201442_init', 1446809305);

-- --------------------------------------------------------

--
-- Структура таблицы `dpd_news`
--

CREATE TABLE `dpd_news` (
  `id` int(10) unsigned NOT NULL COMMENT 'ID',
  `name` varchar(255) NOT NULL COMMENT 'Название',
  `published_date` datetime NOT NULL COMMENT 'Дата публикации',
  `intro` text NOT NULL COMMENT 'Вводный текст',
  `content` mediumtext NOT NULL COMMENT 'Текст',
  `meta_title` varchar(255) DEFAULT NULL COMMENT 'META Title',
  `meta_description` varchar(255) DEFAULT NULL COMMENT 'META Description',
  `meta_keywords` varchar(255) DEFAULT NULL COMMENT 'META Keywords',
  `created_date` datetime NOT NULL COMMENT 'Дата создания',
  `last_update_date` datetime NOT NULL COMMENT 'Дата последнего обновления',
  `status` int(1) unsigned NOT NULL DEFAULT '1' COMMENT 'Статус'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `dpd_page`
--

CREATE TABLE `dpd_page` (
  `id` int(10) unsigned NOT NULL COMMENT 'IВ',
  `name` varchar(255) NOT NULL COMMENT 'Название',
  `content` longtext NOT NULL COMMENT 'Содержимое',
  `meta_title` varchar(255) DEFAULT NULL COMMENT 'META Title',
  `meta_description` varchar(255) DEFAULT NULL COMMENT 'META Description',
  `meta_keywords` varchar(255) DEFAULT NULL COMMENT 'META Keywords',
  `last_update_date` datetime NOT NULL COMMENT 'Дата последнего обновления',
  `created_date` datetime NOT NULL COMMENT 'Дата создания',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT 'Опубликовать'
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='Таблица предназначена для хранения содержимого статических с';

--
-- Дамп данных таблицы `dpd_page`
--

INSERT INTO `dpd_page` (`id`, `name`, `content`, `meta_title`, `meta_description`, `meta_keywords`, `last_update_date`, `created_date`, `status`) VALUES
(1, 'Методы нанесения', '<p>Раздел на стадии разработки.</p>', '', '', '', '2015-11-10 19:22:07', '2015-11-09 17:28:04', 1),
(2, 'Дари по делу', '<p>Страница на стадии разработки.</p>', '', '', '', '2015-11-09 17:29:57', '2015-11-09 17:29:57', 1),
(3, 'Шелкография', '<p>Страница на стадии разработки</p>', '', '', '', '2015-11-10 19:41:30', '2015-11-10 19:41:30', 1),
(4, 'Флекс', '<p>Страница на стадии разработки.</p>', '', '', '', '2015-11-10 19:42:22', '2015-11-10 19:42:22', 1),
(5, 'Вышивка', '<p>Страница на стадии разработки.</p>', '', '', '', '2015-11-10 19:44:13', '2015-11-10 19:44:13', 1),
(6, 'Тампопечать', '<p>Страница на стадии разработки.</p>', '', '', '', '2015-11-10 19:45:04', '2015-11-10 19:45:04', 1),
(7, 'УФ-печать', '<p>Страница на стадии разработки.</p>', '', '', '', '2015-11-10 19:46:28', '2015-11-10 19:46:28', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `dpd_print`
--

CREATE TABLE `dpd_print` (
  `name` varchar(20) NOT NULL COMMENT 'Название',
  `description` varchar(255) DEFAULT NULL COMMENT 'Описание'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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

-- --------------------------------------------------------

--
-- Структура таблицы `dpd_product_attachment`
--

CREATE TABLE `dpd_product_attachment` (
  `product_id` int(11) NOT NULL COMMENT 'ID товара',
  `meaning` int(1) NOT NULL COMMENT 'Тип файла',
  `file` varchar(255) DEFAULT NULL COMMENT 'URL доп. файла',
  `image` varchar(255) DEFAULT NULL COMMENT 'URL доп. картинки',
  `name` varchar(255) DEFAULT NULL COMMENT 'Описание'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `dpd_product_filter`
--

CREATE TABLE `dpd_product_filter` (
  `product_id` int(11) NOT NULL COMMENT 'ID товара',
  `filter_id` int(11) NOT NULL COMMENT 'ID фильтра',
  `type_id` int(11) NOT NULL COMMENT 'Тип фильтра'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `dpd_product_print`
--

CREATE TABLE `dpd_product_print` (
  `product_id` int(11) NOT NULL COMMENT 'ID товара',
  `print_id` varchar(20) NOT NULL COMMENT 'ID вида печати'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `dpd_seo`
--

CREATE TABLE `dpd_seo` (
  `id` int(11) NOT NULL COMMENT 'ID',
  `module_id` varchar(40) DEFAULT NULL COMMENT 'Модуль',
  `controller_id` varchar(40) NOT NULL COMMENT 'Контроллер',
  `action_id` varchar(40) NOT NULL COMMENT 'Действие',
  `ctg_id` int(11) DEFAULT NULL COMMENT 'Категория',
  `item_id` int(11) DEFAULT NULL COMMENT 'Элемент',
  `heading` varchar(255) DEFAULT NULL COMMENT 'Заголовок',
  `meta_title` varchar(255) DEFAULT NULL COMMENT 'META Title',
  `meta_description` varchar(255) DEFAULT NULL COMMENT 'META Description',
  `meta_keywords` varchar(255) DEFAULT NULL COMMENT 'META Keywords'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `dpd_session`
--

CREATE TABLE `dpd_session` (
  `id` char(40) NOT NULL,
  `expire` int(11) NOT NULL,
  `data` longblob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `dpd_session`
--

INSERT INTO `dpd_session` (`id`, `expire`, `data`) VALUES
('1q0d4qsmfebt06khitt8qmdgs4', 1447096477, 0x5f5f666c6173687c613a303a7b7d),
('2n55tl1rougbvnqaat6vnogqm2', 1447092542, 0x5f5f666c6173687c613a303a7b7d),
('3fqj2enraqqvm3lfhqoaslu3t1', 1446819213, 0x5f5f666c6173687c613a303a7b7d),
('6aq4sdce7hf6o5lc02e593d7r4', 1448563042, 0x5f5f666c6173687c613a303a7b7d),
('a5034ggimrkghuievtad85srp1', 1447090161, 0x5f5f666c6173687c613a303a7b7d),
('bljnkup77uhju7iofms5qfvel2', 1447008590, 0x5f5f666c6173687c613a303a7b7d),
('glhbr47jnkhjugj839qj7tpgi6', 1447186254, 0x5f5f666c6173687c613a303a7b7d),
('itc1jpeqi2cmcvir61if3fh0c0', 1447096476, 0x5f5f666c6173687c613a303a7b7d5f5f72657475726e55726c7c733a373a222f61646d696e2f223b5f5f69647c693a313b),
('j9sdrdoiksklk5dmjb2e6s2gc2', 1447186253, 0x5f5f666c6173687c613a303a7b7d5f5f69647c693a313b),
('k8pvf1j9tc04ocjlghiso6nb35', 1448399766, 0x5f5f666c6173687c613a303a7b7d5f5f69647c693a313b),
('l0mnvt41irkh0ssgkep9t1kuh1', 1448399767, 0x5f5f666c6173687c613a303a7b7d),
('mq44b6ivuo007fri6ielpmr7d3', 1446812772, 0x5f5f666c6173687c613a303a7b7d),
('oqg92pjd3ddha7al1b1g6q2td4', 1447008588, 0x5f5f666c6173687c613a303a7b7d5f5f69647c693a313b),
('r0o1ha9ns1tfv3pshi0ajqqe80', 1448744442, 0x5f5f666c6173687c613a303a7b7d5f5f69647c693a313b),
('ue53an7l0erq3ev9u3gd4nrm00', 1446819212, 0x5f5f666c6173687c613a303a7b7d5f5f69647c693a313b),
('vegr5omuu9kcdohjb3p2k3ra43', 1448745932, 0x5f5f666c6173687c613a303a7b7d);

-- --------------------------------------------------------

--
-- Структура таблицы `dpd_slave_product`
--

CREATE TABLE `dpd_slave_product` (
  `id` int(11) NOT NULL COMMENT 'ID товара',
  `parent_product_id` int(11) NOT NULL COMMENT 'ID родительского товара',
  `code` varchar(100) DEFAULT NULL COMMENT 'Артикул',
  `name` varchar(255) DEFAULT NULL COMMENT 'Название',
  `size_code` varchar(255) DEFAULT NULL COMMENT 'Размер',
  `weight` float(9,2) NOT NULL DEFAULT '0.00' COMMENT 'Вес',
  `price` float(15,2) NOT NULL DEFAULT '0.00' COMMENT 'Цена',
  `price_currency` varchar(20) DEFAULT NULL COMMENT 'Валюта',
  `price_name` varchar(40) DEFAULT NULL COMMENT 'Название цены'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `dpd_user`
--

CREATE TABLE `dpd_user` (
  `id` int(11) NOT NULL,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `auth_key` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password_reset_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` smallint(6) NOT NULL DEFAULT '10',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Дамп данных таблицы `dpd_user`
--

INSERT INTO `dpd_user` (`id`, `username`, `auth_key`, `password_hash`, `password_reset_token`, `email`, `status`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'i-kGsiym5DPaCfvhz_cEfRSGnpWiNraS', '$2y$13$vGN/dE8cOpiyHuIhqnyZKeOJUsDstmFzGYGr6xnpLwD7ReHriW/KC', NULL, 'ratmir85@gmail.com', 10, 1446811328, 1446811328);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `dpd_catalogue`
--
ALTER TABLE `dpd_catalogue`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Индексы таблицы `dpd_config`
--
ALTER TABLE `dpd_config`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `param` (`param`);

--
-- Индексы таблицы `dpd_filter`
--
ALTER TABLE `dpd_filter`
  ADD PRIMARY KEY (`type_id`,`id`),
  ADD KEY `id` (`id`);

--
-- Индексы таблицы `dpd_filter_type`
--
ALTER TABLE `dpd_filter_type`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `dpd_image`
--
ALTER TABLE `dpd_image`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `file_name` (`file_name`),
  ADD KEY `ctg_id` (`ctg_id`);

--
-- Индексы таблицы `dpd_menu_tree`
--
ALTER TABLE `dpd_menu_tree`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `depth_alias` (`depth`,`alias`),
  ADD KEY `lft` (`lft`),
  ADD KEY `rgt` (`rgt`),
  ADD KEY `depth` (`depth`),
  ADD KEY `alias` (`alias`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Индексы таблицы `dpd_migration`
--
ALTER TABLE `dpd_migration`
  ADD PRIMARY KEY (`version`);

--
-- Индексы таблицы `dpd_news`
--
ALTER TABLE `dpd_news`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `dpd_page`
--
ALTER TABLE `dpd_page`
  ADD PRIMARY KEY (`id`),
  ADD KEY `status` (`status`);

--
-- Индексы таблицы `dpd_print`
--
ALTER TABLE `dpd_print`
  ADD PRIMARY KEY (`name`);

--
-- Индексы таблицы `dpd_product`
--
ALTER TABLE `dpd_product`
  ADD PRIMARY KEY (`id`),
  ADD KEY `dpd_product_fk0` (`catalogue_id`);

--
-- Индексы таблицы `dpd_product_attachment`
--
ALTER TABLE `dpd_product_attachment`
  ADD KEY `dpd_product_attachment_fk0` (`product_id`);

--
-- Индексы таблицы `dpd_product_filter`
--
ALTER TABLE `dpd_product_filter`
  ADD KEY `dpd_product_filter_fk0` (`product_id`),
  ADD KEY `dpd_product_filter_fk1` (`filter_id`),
  ADD KEY `type_id` (`type_id`,`filter_id`);

--
-- Индексы таблицы `dpd_product_print`
--
ALTER TABLE `dpd_product_print`
  ADD KEY `dpd_product_print_fk0` (`product_id`),
  ADD KEY `print_id` (`print_id`);

--
-- Индексы таблицы `dpd_seo`
--
ALTER TABLE `dpd_seo`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `i_mcaci` (`module_id`,`controller_id`,`action_id`,`ctg_id`,`item_id`),
  ADD KEY `i_mca` (`module_id`,`controller_id`,`action_id`);

--
-- Индексы таблицы `dpd_session`
--
ALTER TABLE `dpd_session`
  ADD PRIMARY KEY (`id`),
  ADD KEY `expire` (`expire`);

--
-- Индексы таблицы `dpd_slave_product`
--
ALTER TABLE `dpd_slave_product`
  ADD PRIMARY KEY (`id`),
  ADD KEY `dpd_slave_product_fk0` (`parent_product_id`);

--
-- Индексы таблицы `dpd_user`
--
ALTER TABLE `dpd_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `password_reset_token` (`password_reset_token`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `dpd_config`
--
ALTER TABLE `dpd_config`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT для таблицы `dpd_image`
--
ALTER TABLE `dpd_image`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID';
--
-- AUTO_INCREMENT для таблицы `dpd_menu_tree`
--
ALTER TABLE `dpd_menu_tree`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT для таблицы `dpd_news`
--
ALTER TABLE `dpd_news`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID';
--
-- AUTO_INCREMENT для таблицы `dpd_page`
--
ALTER TABLE `dpd_page`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'IВ',AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT для таблицы `dpd_seo`
--
ALTER TABLE `dpd_seo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID';
--
-- AUTO_INCREMENT для таблицы `dpd_user`
--
ALTER TABLE `dpd_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `dpd_filter`
--
ALTER TABLE `dpd_filter`
  ADD CONSTRAINT `dpd_filter_fk0` FOREIGN KEY (`type_id`) REFERENCES `dpd_filter_type` (`id`);

--
-- Ограничения внешнего ключа таблицы `dpd_product`
--
ALTER TABLE `dpd_product`
  ADD CONSTRAINT `dpd_product_fk0` FOREIGN KEY (`catalogue_id`) REFERENCES `dpd_catalogue` (`id`);

--
-- Ограничения внешнего ключа таблицы `dpd_product_attachment`
--
ALTER TABLE `dpd_product_attachment`
  ADD CONSTRAINT `dpd_product_attachment_fk0` FOREIGN KEY (`product_id`) REFERENCES `dpd_product` (`id`);

--
-- Ограничения внешнего ключа таблицы `dpd_product_filter`
--
ALTER TABLE `dpd_product_filter`
  ADD CONSTRAINT `dpd_multfk` FOREIGN KEY (`type_id`, `filter_id`) REFERENCES `dpd_filter` (`type_id`, `id`),
  ADD CONSTRAINT `dpd_product_filter_fk0` FOREIGN KEY (`product_id`) REFERENCES `dpd_product` (`id`);

--
-- Ограничения внешнего ключа таблицы `dpd_product_print`
--
ALTER TABLE `dpd_product_print`
  ADD CONSTRAINT `dpd_product_print_ibfk_1` FOREIGN KEY (`print_id`) REFERENCES `dpd_print` (`name`),
  ADD CONSTRAINT `dpd_product_print_fk0` FOREIGN KEY (`product_id`) REFERENCES `dpd_product` (`id`);

--
-- Ограничения внешнего ключа таблицы `dpd_slave_product`
--
ALTER TABLE `dpd_slave_product`
  ADD CONSTRAINT `dpd_slave_product_fk0` FOREIGN KEY (`parent_product_id`) REFERENCES `dpd_product` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
