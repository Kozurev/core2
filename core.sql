-- phpMyAdmin SQL Dump
-- version 4.6.5.2
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Мар 18 2018 г., 22:45
-- Версия сервера: 5.5.53
-- Версия PHP: 7.0.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `core`
--

-- --------------------------------------------------------

--
-- Структура таблицы `Admin_Form`
--

CREATE TABLE `Admin_Form` (
  `id` int(11) NOT NULL,
  `model_id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `var_name` varchar(50) NOT NULL,
  `maxlength` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  `active` int(11) NOT NULL,
  `sorting` int(11) NOT NULL,
  `list_name` varchar(50) NOT NULL,
  `value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `Admin_Form`
--

INSERT INTO `Admin_Form` (`id`, `model_id`, `title`, `var_name`, `maxlength`, `type_id`, `active`, `sorting`, `list_name`, `value`) VALUES
(1, 1, 'Заголовок', 'title', 150, 2, 1, 1, '', ''),
(2, 1, 'Путь', 'path', 100, 2, 1, 2, '', ''),
(3, 1, 'Активость', 'active', 0, 3, 1, 3, '', ''),
(4, 1, 'Файл обработчик', 'action', 100, 2, 1, 4, '', ''),
(5, 1, 'Родительский раздел', 'parentId', 0, 4, 1, 5, 'Structures', ''),
(6, 1, 'Макет', 'template_id', 0, 4, 1, 6, 'Templates', ''),
(7, 1, 'Описание', 'description', 2000, 5, 1, 7, '', ''),
(8, 2, 'Название', 'title', 150, 2, 1, 1, '', ''),
(9, 2, 'Путь', 'path', 50, 2, 1, 2, '', ''),
(10, 1, 'Сортировка', 'sorting', 0, 1, 1, 8, '', ''),
(12, 2, 'Сортировка', 'sorting', 0, 1, 1, 4, '', ''),
(13, 2, 'Активность', 'active', 0, 3, 1, 3, '', ''),
(14, 2, 'Родительский раздел', 'parentId', 0, 4, 1, 3, 'Structures', ''),
(15, 3, 'Заголовок', 'title', 150, 2, 1, 1, '', ''),
(16, 3, 'Название константы (в верхнем регистре)', 'name', 150, 2, 1, 2, '', ''),
(17, 3, 'Описание', 'description', 2000, 5, 1, 3, '', ''),
(19, 3, 'Значение', 'value', 2000, 5, 1, 4, '', ''),
(20, 3, 'Тип значения', 'valueType', 0, 4, 1, 5, 'ConstantTypes', ''),
(21, 3, 'Активность', 'active', 0, 3, 1, 3, '', ''),
(22, 3, 'Родительская директория', 'dir', 0, 4, 1, 4, 'ConstantDirs', ''),
(23, 4, 'Заголовок', 'title', 150, 2, 1, 1, '', ''),
(24, 4, 'Описание', 'description', 2000, 5, 1, 2, '', ''),
(25, 4, 'Родительский раздел', 'parentId', 0, 4, 1, 3, 'ConstantDirs', ''),
(26, 4, 'Сортировка', 'sorting', 0, 1, 1, 4, '', '');

-- --------------------------------------------------------

--
-- Структура таблицы `Admin_Form_Modelname`
--

CREATE TABLE `Admin_Form_Modelname` (
  `id` int(11) NOT NULL,
  `model_name` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `Admin_Form_Modelname`
--

INSERT INTO `Admin_Form_Modelname` (`id`, `model_name`) VALUES
(1, 'Structure'),
(2, 'Structure_Item'),
(3, 'Constant'),
(4, 'Constant_Dir');

-- --------------------------------------------------------

--
-- Структура таблицы `Admin_Form_Type`
--

CREATE TABLE `Admin_Form_Type` (
  `id` int(11) NOT NULL,
  `title` varchar(50) NOT NULL,
  `input_type` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `Admin_Form_Type`
--

INSERT INTO `Admin_Form_Type` (`id`, `title`, `input_type`) VALUES
(1, 'Число', 'number'),
(2, 'Строка', 'text'),
(3, 'Флажок', 'checkbox'),
(4, 'Список', ''),
(5, 'Текст', '');

-- --------------------------------------------------------

--
-- Структура таблицы `Admin_Menu`
--

CREATE TABLE `Admin_Menu` (
  `id` int(11) NOT NULL,
  `title` varchar(50) NOT NULL,
  `model` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `Admin_Menu`
--

INSERT INTO `Admin_Menu` (`id`, `title`, `model`) VALUES
(1, 'Главная', 'Main'),
(2, 'Структуры', 'Structure'),
(3, 'Пользователи', 'User'),
(4, 'Константы', 'Constant'),
(5, 'Формы редактирования', 'Form');

-- --------------------------------------------------------

--
-- Структура таблицы `Constant`
--

CREATE TABLE `Constant` (
  `id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` text NOT NULL,
  `value` varchar(150) NOT NULL,
  `value_type` int(11) NOT NULL,
  `dir` int(11) NOT NULL,
  `active` int(11) NOT NULL,
  `sorting` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `Constant`
--

INSERT INTO `Constant` (`id`, `title`, `name`, `description`, `value`, `value_type`, `dir`, `active`, `sorting`) VALUES
(4, 'Пагинация в админ. разделе', 'SHOW_LIMIT', 'Лимит показов количества структур и объектов структур', '30', 0, 1, 1, 0),
(5, 'Тестовая константа 1', 'TEST_1', 'Какая-то константа №1', 'test1', 0, 0, 1, 0),
(6, 'Тестовая константа 2', 'TEST_2', 'Какая-то константа №2', '5', 1, 1, 1, 0),
(11, 'Новая константа', 'NEW', 'Описание константы', '700', 1, 1, 1, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `Constant_Dir`
--

CREATE TABLE `Constant_Dir` (
  `id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `description` text NOT NULL,
  `parent_id` int(11) NOT NULL,
  `sorting` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `Constant_Dir`
--

INSERT INTO `Constant_Dir` (`id`, `title`, `description`, `parent_id`, `sorting`) VALUES
(1, 'Системные константы', 'Константы содержащие основные настройки системы', 0, 0),
(2, 'Тестовый раздел констант', 'Описание тестового раздела констант', 0, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `Constant_Type`
--

CREATE TABLE `Constant_Type` (
  `id` int(11) NOT NULL,
  `title` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `Constant_Type`
--

INSERT INTO `Constant_Type` (`id`, `title`) VALUES
(1, 'int'),
(2, 'float'),
(3, 'bool');

-- --------------------------------------------------------

--
-- Структура таблицы `Page_Template`
--

CREATE TABLE `Page_Template` (
  `id` int(11) NOT NULL,
  `title` varchar(50) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `dir` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `Page_Template`
--

INSERT INTO `Page_Template` (`id`, `title`, `parent_id`, `dir`) VALUES
(1, 'Макет для страницы авторизации', 0, 0),
(2, 'Вложенный макет', 1, 0),
(3, 'Административный раздел', 0, 0),
(4, 'Главный макет musadm', 0, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `Page_Template_Dir`
--

CREATE TABLE `Page_Template_Dir` (
  `id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `Property`
--

CREATE TABLE `Property` (
  `id` int(11) NOT NULL,
  `tag_name` varchar(50) NOT NULL,
  `title` varchar(150) NOT NULL,
  `description` text NOT NULL,
  `type` varchar(50) NOT NULL,
  `active` int(11) NOT NULL DEFAULT '1',
  `dir` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `Property`
--

INSERT INTO `Property` (`id`, `tag_name`, `title`, `description`, `type`, `active`, `dir`) VALUES
(1, 'price', 'Цена', '', 'int', 1, 0),
(2, 'count', 'Количество', '', 'int', 1, 0),
(3, 'params', 'Параметры', '', 'text', 1, 0),
(4, 'description', 'Описание', '', 'text', 1, 0),
(5, 'list', 'Тестовый список', '', 'list', 1, 0),
(6, 'test', 'Тестовой свойство', '', 'int', 1, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `Property_Dir`
--

CREATE TABLE `Property_Dir` (
  `id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `Property_Int`
--

CREATE TABLE `Property_Int` (
  `id` int(11) NOT NULL,
  `property_id` int(11) NOT NULL,
  `value` float NOT NULL,
  `model_name` varchar(100) NOT NULL,
  `object_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `Property_Int`
--

INSERT INTO `Property_Int` (`id`, `property_id`, `value`, `model_name`, `object_id`) VALUES
(1, 1, 100, 'Structure_Item', 2),
(2, 1, 500, 'Structure_Item', 2),
(3, 1, 1000, 'Structure_Item', 2),
(6, 1, 77712, 'Structure', 1),
(8, 1, 88812, 'Structure', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `Property_List`
--

CREATE TABLE `Property_List` (
  `id` int(11) NOT NULL,
  `property_id` int(11) NOT NULL,
  `model_name` varchar(100) NOT NULL,
  `object_id` int(11) NOT NULL,
  `value_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `Property_List`
--

INSERT INTO `Property_List` (`id`, `property_id`, `model_name`, `object_id`, `value_id`) VALUES
(1, 5, 'Structure_Item', 2, 1),
(2, 5, 'Structure', 1, 4);

-- --------------------------------------------------------

--
-- Структура таблицы `Property_List_Values`
--

CREATE TABLE `Property_List_Values` (
  `id` int(11) NOT NULL,
  `property_id` int(11) NOT NULL,
  `value` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `Property_List_Values`
--

INSERT INTO `Property_List_Values` (`id`, `property_id`, `value`) VALUES
(1, 5, 'Значение 1'),
(2, 5, 'Значение 2'),
(3, 5, 'Значение 3'),
(4, 5, 'Значение 4');

-- --------------------------------------------------------

--
-- Структура таблицы `Property_Text`
--

CREATE TABLE `Property_Text` (
  `id` int(11) NOT NULL,
  `property_id` int(11) NOT NULL,
  `value` text NOT NULL,
  `model_name` varchar(100) NOT NULL,
  `object_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `Property_Text`
--

INSERT INTO `Property_Text` (`id`, `property_id`, `value`, `model_name`, `object_id`) VALUES
(1, 4, 'Описание товара', 'Structure_Item', 2),
(4, 3, 'Какое-то значение дополнительного свойства \"Параметры\"', 'Structure', 1),
(5, 3, 'параметры', 'Structure_Item', 2);

-- --------------------------------------------------------

--
-- Структура таблицы `Structure`
--

CREATE TABLE `Structure` (
  `id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `path` varchar(100) NOT NULL,
  `action` varchar(100) NOT NULL,
  `template_id` int(11) NOT NULL,
  `description` text NOT NULL,
  `properties_list` text NOT NULL,
  `active` int(11) NOT NULL DEFAULT '1',
  `sorting` int(11) NOT NULL,
  `meta_title` varchar(100) NOT NULL,
  `meta_description` text NOT NULL,
  `meta_keywords` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `Structure`
--

INSERT INTO `Structure` (`id`, `title`, `parent_id`, `path`, `action`, `template_id`, `description`, `properties_list`, `active`, `sorting`, `meta_title`, `meta_description`, `meta_keywords`) VALUES
(1, 'Магазин', 0, 'shop', 'catalog', 1, 'Интернет-магазин', 'a:2:{i:0;i:1;i:1;i:5;}', 1, 0, '', '', ''),
(2, 'Спорт', 1, 'sport', 'catalog', 1, '', '', 1, 0, '', '', ''),
(3, 'Теннис', 2, 'tennis', 'catalog', 1, '', '', 1, 0, '', '', ''),
(4, 'Футбол', 2, 'football', 'catalog', 1, '', '', 1, 0, '', '', ''),
(5, 'Индексная страница', 0, '', 'index', 1, '', '', 1, 0, '', '', ''),
(6, 'Административный раздел', 0, 'admin', 'admin/index', 3, '', 'b:0;', 1, 0, '', '', ''),
(7, 'Личный кабинет', 0, 'user', 'user', 2, '', '', 1, 0, '', '', ''),
(8, 'Иерархия классов', 9, 'models', 'documentation/models', 1, 'Общая структура системы. Описание стандартных классов, их свойств и методов.', '', 1, 0, '', '', ''),
(9, 'Документация', 0, 'documentation', 'documentation/index', 1, 'Руководство по использованию системы', '', 1, 0, '', '', ''),
(13, 'Панель управления', 0, 'musadm', 'musadm/index', 4, '', 'b:0;', 1, 0, '', '', ''),
(14, 'Авторизация', 13, 'authorize', 'musadm/authorize', 1, '', 'b:0;', 1, 0, '', '', '');

-- --------------------------------------------------------

--
-- Структура таблицы `Structure_Item`
--

CREATE TABLE `Structure_Item` (
  `id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `description` text NOT NULL,
  `properties_list` text NOT NULL,
  `active` int(11) NOT NULL DEFAULT '1',
  `meta_title` varchar(100) NOT NULL,
  `meta_description` text NOT NULL,
  `meta_keywords` varchar(100) NOT NULL,
  `path` varchar(50) NOT NULL,
  `sorting` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `Structure_Item`
--

INSERT INTO `Structure_Item` (`id`, `title`, `parent_id`, `description`, `properties_list`, `active`, `meta_title`, `meta_description`, `meta_keywords`, `path`, `sorting`) VALUES
(1, 'Футбольный мячь', 4, '', '', 1, 'SEO title', 'SEO описание', 'SEO ключевые слова', '1', 0),
(2, 'Кросовки Nike', 4, '', 'a:4:{i:0;i:1;i:1;i:5;i:2;i:3;i:3;i:4;}', 1, '', '', '', '2', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `User`
--

CREATE TABLE `User` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `surname` varchar(50) NOT NULL,
  `patronimyc` varchar(50) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `email` varchar(50) NOT NULL,
  `login` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `group_id` int(11) NOT NULL DEFAULT '2',
  `register_date` date NOT NULL,
  `active` int(11) NOT NULL DEFAULT '1',
  `superuser` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `User`
--

INSERT INTO `User` (`id`, `name`, `surname`, `patronimyc`, `phone_number`, `email`, `login`, `password`, `group_id`, `register_date`, `active`, `superuser`) VALUES
(1, 'Егор', 'Козырев', 'Алексеевич', '8-980-378-28-56', 'creative27016@gmail.com', 'alexoufx', '4a7d1ed414474e4033ac29ccb8653d9b', 1, '0000-00-00', 1, 1),
(2, 'Имя', 'Фамилия', '', '8-980-888-88-88', 'test@email.ru', 'test', '098f6bcd4621d373cade4e832627b4f6', 2, '2018-02-15', 1, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `User_Group`
--

CREATE TABLE `User_Group` (
  `id` int(11) NOT NULL,
  `title` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `User_Group`
--

INSERT INTO `User_Group` (`id`, `title`) VALUES
(1, 'Администратор'),
(2, 'Пользователь');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `Admin_Form`
--
ALTER TABLE `Admin_Form`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `Admin_Form_Modelname`
--
ALTER TABLE `Admin_Form_Modelname`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `Admin_Form_Type`
--
ALTER TABLE `Admin_Form_Type`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `Admin_Menu`
--
ALTER TABLE `Admin_Menu`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `Constant`
--
ALTER TABLE `Constant`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `Constant_Dir`
--
ALTER TABLE `Constant_Dir`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `Constant_Type`
--
ALTER TABLE `Constant_Type`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `Page_Template`
--
ALTER TABLE `Page_Template`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `Page_Template_Dir`
--
ALTER TABLE `Page_Template_Dir`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `Property`
--
ALTER TABLE `Property`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `Property_Dir`
--
ALTER TABLE `Property_Dir`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `Property_Int`
--
ALTER TABLE `Property_Int`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `Property_List`
--
ALTER TABLE `Property_List`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `Property_List_Values`
--
ALTER TABLE `Property_List_Values`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `Property_Text`
--
ALTER TABLE `Property_Text`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `Structure`
--
ALTER TABLE `Structure`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `Structure_Item`
--
ALTER TABLE `Structure_Item`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `User`
--
ALTER TABLE `User`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `User_Group`
--
ALTER TABLE `User_Group`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `Admin_Form`
--
ALTER TABLE `Admin_Form`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;
--
-- AUTO_INCREMENT для таблицы `Admin_Form_Modelname`
--
ALTER TABLE `Admin_Form_Modelname`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT для таблицы `Admin_Form_Type`
--
ALTER TABLE `Admin_Form_Type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT для таблицы `Admin_Menu`
--
ALTER TABLE `Admin_Menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT для таблицы `Constant`
--
ALTER TABLE `Constant`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT для таблицы `Constant_Dir`
--
ALTER TABLE `Constant_Dir`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT для таблицы `Constant_Type`
--
ALTER TABLE `Constant_Type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT для таблицы `Page_Template`
--
ALTER TABLE `Page_Template`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT для таблицы `Page_Template_Dir`
--
ALTER TABLE `Page_Template_Dir`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `Property`
--
ALTER TABLE `Property`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT для таблицы `Property_Dir`
--
ALTER TABLE `Property_Dir`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `Property_Int`
--
ALTER TABLE `Property_Int`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT для таблицы `Property_List`
--
ALTER TABLE `Property_List`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT для таблицы `Property_List_Values`
--
ALTER TABLE `Property_List_Values`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT для таблицы `Property_Text`
--
ALTER TABLE `Property_Text`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT для таблицы `Structure`
--
ALTER TABLE `Structure`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
--
-- AUTO_INCREMENT для таблицы `Structure_Item`
--
ALTER TABLE `Structure_Item`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT для таблицы `User`
--
ALTER TABLE `User`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT для таблицы `User_Group`
--
ALTER TABLE `User_Group`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
