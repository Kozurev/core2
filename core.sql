-- phpMyAdmin SQL Dump
-- version 4.6.5.2
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Апр 10 2018 г., 20:37
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
  `title` varchar(150) NOT NULL DEFAULT '',
  `var_name` varchar(50) NOT NULL DEFAULT '',
  `maxlength` int(11) NOT NULL DEFAULT '0',
  `type_id` int(11) NOT NULL DEFAULT '0',
  `active` int(11) NOT NULL DEFAULT '1',
  `required` int(11) NOT NULL DEFAULT '0',
  `sorting` int(11) NOT NULL DEFAULT '0',
  `list_name` varchar(50) NOT NULL DEFAULT '',
  `value` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `Admin_Form`
--

INSERT INTO `Admin_Form` (`id`, `model_id`, `title`, `var_name`, `maxlength`, `type_id`, `active`, `required`, `sorting`, `list_name`, `value`) VALUES
(1, 1, 'Заголовок', 'title', 150, 2, 1, 1, 1, '', ''),
(2, 1, 'Путь', 'path', 100, 2, 1, 0, 2, '', ''),
(3, 1, 'Активость', 'active', 0, 3, 1, 0, 3, '', ''),
(4, 1, 'Файл обработчик', 'action', 100, 2, 1, 1, 4, '', ''),
(5, 1, 'Родительский раздел', 'parentId', 0, 4, 1, 0, 5, 'Structures', ''),
(6, 1, 'Макет', 'template_id', 0, 4, 1, 1, 6, 'Templates', ''),
(7, 1, 'Описание', 'description', 2000, 5, 1, 0, 7, '', ''),
(8, 2, 'Название', 'title', 150, 2, 1, 1, 1, '', ''),
(9, 2, 'Путь', 'path', 50, 2, 1, 0, 2, '', ''),
(10, 1, 'Сортировка', 'sorting', 0, 1, 1, 0, 8, '', ''),
(12, 2, 'Сортировка', 'sorting', 0, 1, 1, 0, 4, '', ''),
(13, 2, 'Активность', 'active', 0, 3, 1, 0, 3, '', ''),
(14, 2, 'Родительский раздел', 'parentId', 0, 4, 1, 0, 3, 'Structures', ''),
(15, 3, 'Заголовок', 'title', 150, 2, 1, 1, 1, '', ''),
(16, 3, 'Название константы (в верхнем регистре)', 'name', 150, 2, 1, 1, 2, '', ''),
(17, 3, 'Описание', 'description', 2000, 5, 1, 0, 3, '', ''),
(19, 3, 'Значение', 'value', 2000, 5, 1, 1, 4, '', ''),
(20, 3, 'Тип значения', 'valueType', 0, 4, 1, 0, 5, 'ConstantTypes', ''),
(21, 3, 'Активность', 'active', 0, 3, 1, 0, 3, '', ''),
(22, 3, 'Родительская директория', 'dir', 0, 4, 1, 0, 4, 'ConstantDirsForC', ''),
(23, 4, 'Заголовок', 'title', 150, 2, 1, 1, 1, '', ''),
(24, 4, 'Описание', 'description', 2000, 5, 1, 0, 2, '', ''),
(25, 4, 'Родительский раздел', 'parentId', 0, 4, 1, 0, 3, 'ConstantDirsForD', ''),
(26, 4, 'Сортировка', 'sorting', 0, 1, 1, 0, 4, '', ''),
(27, 6, 'Название группы', 'title', 50, 2, 1, 1, 0, '', ''),
(28, 6, 'Сортировка', 'sorting', 0, 1, 1, 0, 0, '', ''),
(29, 5, 'Имя', 'name', 50, 2, 1, 1, 0, '', ''),
(30, 5, 'Фамилия', 'surname', 50, 2, 1, 1, 10, '', ''),
(31, 5, 'Отчество', 'patronimyc', 50, 2, 1, 0, 20, '', ''),
(32, 5, 'Номер телефона', 'phoneNumber', 100, 2, 1, 0, 30, '', ''),
(33, 5, 'Логин', 'login', 50, 2, 1, 1, 40, '', ''),
(34, 5, 'Пароль', 'pass1', 50, 6, 1, 0, 50, '', ''),
(35, 5, 'Повторите пароль', 'pass2', 50, 6, 1, 0, 60, '', ''),
(36, 7, 'Название модели', 'model_name', 255, 2, 1, 1, 20, '', ''),
(37, 7, 'Заголовок', 'model_title', 150, 2, 1, 1, 10, '', ''),
(38, 7, 'Сортировка', 'model_sorting', 0, 1, 1, 0, 30, '', ''),
(39, 10, 'Заголовок', 'title', 150, 2, 1, 1, 10, '', ''),
(40, 10, 'Название переменной (сеттера)', 'varName', 50, 2, 1, 1, 20, '', ''),
(41, 10, 'Родительская модель', 'model_id', 0, 4, 1, 0, 30, 'AdminFormModelnames', ''),
(42, 10, 'Максимальная длинна поля', 'maxlength', 0, 1, 1, 0, 40, '', ''),
(43, 10, 'Тип поля', 'type_id', 0, 4, 1, 0, 50, 'AdminFormTypes', ''),
(44, 10, 'Активность', 'active', 0, 3, 1, 0, 60, '', ''),
(45, 10, 'Сортировка', 'sorting', 0, 1, 1, 0, 70, '', ''),
(46, 10, 'Название списка', 'listName', 50, 2, 1, 0, 80, '', ''),
(47, 10, 'Значение', 'value', 2000, 5, 1, 0, 90, '', ''),
(49, 10, 'Обязательное поле', 'required', 0, 3, 1, 0, 100, '', ''),
(50, 12, 'Название', 'title', 150, 2, 1, 1, 10, '', ''),
(51, 12, 'Название тэга', 'tag_name', 50, 2, 1, 0, 20, '', ''),
(52, 12, 'Активность', 'active', 0, 3, 1, 0, 30, '', ''),
(53, 12, 'Тип', 'type', 0, 4, 1, 1, 40, 'PropertyTypes', ''),
(54, 12, 'Описание', 'description', 0, 5, 1, 0, 50, '', ''),
(55, 12, 'Родительская дирректория', 'dir', 0, 4, 1, 0, 60, 'PropertyDirs', ''),
(56, 12, 'Сортировка', 'sorting', 0, 1, 1, 0, 70, '', '');

-- --------------------------------------------------------

--
-- Структура таблицы `Admin_Form_Modelname`
--

CREATE TABLE `Admin_Form_Modelname` (
  `id` int(11) NOT NULL,
  `model_name` varchar(150) NOT NULL,
  `model_title` varchar(255) NOT NULL,
  `model_sorting` int(11) NOT NULL,
  `indexing` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `Admin_Form_Modelname`
--

INSERT INTO `Admin_Form_Modelname` (`id`, `model_name`, `model_title`, `model_sorting`, `indexing`) VALUES
(1, 'Structure', 'Структуры', 10, 1),
(2, 'Structure_Item', 'Элементы структур', 20, 1),
(3, 'Constant', 'Константы', 30, 1),
(4, 'Constant_Dir', 'Дирректории констант', 40, 1),
(5, 'User', 'Пользователи', 50, 1),
(6, 'User_Group', 'Группы пользователей', 60, 1),
(7, 'Admin_Form_Modelname', '', 0, 0),
(10, 'Admin_Form', '', 0, 0),
(12, 'Property', 'Дополнительные свойства', 70, 1),
(13, 'Property_Dir', 'Директории доп. свойств', 80, 1);

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
(5, 'Текст', ''),
(6, 'Пароль', 'password');

-- --------------------------------------------------------

--
-- Структура таблицы `Admin_Menu`
--

CREATE TABLE `Admin_Menu` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `model` varchar(255) NOT NULL,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `active` int(11) NOT NULL DEFAULT '1',
  `sorting` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `Admin_Menu`
--

INSERT INTO `Admin_Menu` (`id`, `title`, `model`, `parent_id`, `active`, `sorting`) VALUES
(2, 'Структуры', 'Structure', 7, 1, 10),
(3, 'Пользователи', 'User', 7, 1, 40),
(4, 'Константы', 'Constant', 7, 1, 30),
(5, 'Формы редактирования', 'Form', 7, 1, 50),
(6, 'Макеты', 'Template', 7, 1, 20),
(7, 'Система', '', 0, 1, 10),
(8, 'Musicmethod', 'Musicmethod', 0, 1, 10),
(9, 'Платежи', 'Payment', 8, 1, 10),
(10, 'Тарифы', 'Tarif', 8, 1, 20),
(11, 'Лиды', 'Lid', 8, 1, 30),
(12, 'Сертификаты', 'Sertificates', 8, 1, 40),
(13, 'Дополнительные свойства', 'Property', 7, 1, 45),
(14, 'Списки', 'List', 7, 1, 42);

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
  `dir` int(11) NOT NULL DEFAULT '0',
  `active` int(11) NOT NULL DEFAULT '0',
  `sorting` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `Constant`
--

INSERT INTO `Constant` (`id`, `title`, `name`, `description`, `value`, `value_type`, `dir`, `active`, `sorting`) VALUES
(4, 'Пагинация в админ. разделе', 'SHOW_LIMIT', 'Лимит показов количества структур и объектов структур', '10', 1, 1, 1, 0),
(5, 'kjn', 'knmlkm', '', '2', 1, 4, 1, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `Constant_Dir`
--

CREATE TABLE `Constant_Dir` (
  `id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `description` text,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `sorting` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `Constant_Dir`
--

INSERT INTO `Constant_Dir` (`id`, `title`, `description`, `parent_id`, `sorting`) VALUES
(1, 'Системные константы', 'Константы содержащие основные настройки системы', 0, 0),
(2, 'Тестовый раздел констант', 'Описание тестового раздела констант', 0, 0),
(3, 'Вложенность второго уровня', '123', 2, 0),
(4, 'Вложенность третьего уровня', '123', 3, 0);

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
(3, 'Административный раздел', 0, 0),
(4, 'Главный макет musadm', 0, 0),
(5, 'Пустой макет', 0, 0);

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
  `dir` int(11) NOT NULL,
  `sorting` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `Property`
--

INSERT INTO `Property` (`id`, `tag_name`, `title`, `description`, `type`, `active`, `dir`, `sorting`) VALUES
(1, 'price', 'Цена', '', 'list', 1, 0, 0),
(2, 'count', 'Количество', '', 'int', 1, 0, 0),
(3, 'params', 'Параметры', '', 'string', 1, 0, 0),
(4, 'description', 'Описание', '', 'text', 1, 0, 0),
(9, 'vk', 'Ссылка вконтакте', '', 'string', 1, 1, 0),
(10, 'lesson_type', 'Тип урока', '', 'list', 1, 1, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `Property_Dir`
--

CREATE TABLE `Property_Dir` (
  `id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `description` text NOT NULL,
  `dir` int(11) NOT NULL,
  `sorting` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `Property_Dir`
--

INSERT INTO `Property_Dir` (`id`, `title`, `description`, `dir`, `sorting`) VALUES
(1, 'musadm', '', 0, 0);

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
(435, 2, 5, 'Structure_Item', 1),
(436, 2, 6, 'Structure_Item', 2);

-- --------------------------------------------------------

--
-- Структура таблицы `Property_Int_Assigment`
--

CREATE TABLE `Property_Int_Assigment` (
  `id` int(11) NOT NULL,
  `property_id` int(11) NOT NULL,
  `object_id` int(11) NOT NULL,
  `model_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `Property_Int_Assigment`
--

INSERT INTO `Property_Int_Assigment` (`id`, `property_id`, `object_id`, `model_name`) VALUES
(1, 2, 1, 'Structure_Item'),
(2, 2, 2, 'Structure_Item');

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
(4, 1, 'Structure_Item', 2, 7),
(5, 1, 'Structure_Item', 1, 10);

-- --------------------------------------------------------

--
-- Структура таблицы `Property_List_Assigment`
--

CREATE TABLE `Property_List_Assigment` (
  `id` int(11) NOT NULL,
  `property_id` int(11) NOT NULL,
  `object_id` int(11) NOT NULL,
  `model_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `Property_List_Assigment`
--

INSERT INTO `Property_List_Assigment` (`id`, `property_id`, `object_id`, `model_name`) VALUES
(1, 1, 1, 'Structure_Item'),
(2, 1, 2, 'Structure_Item');

-- --------------------------------------------------------

--
-- Структура таблицы `Property_List_Values`
--

CREATE TABLE `Property_List_Values` (
  `id` int(11) NOT NULL,
  `property_id` int(11) NOT NULL,
  `value` varchar(100) NOT NULL,
  `sorting` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `Property_List_Values`
--

INSERT INTO `Property_List_Values` (`id`, `property_id`, `value`, `sorting`) VALUES
(7, 1, '400', 0),
(8, 1, '300', 0),
(9, 1, '200', 0),
(10, 1, '250', 0),
(11, 1, '150', 0),
(12, 1, '100', 0),
(13, 1, '0', 0),
(14, 10, 'Индивидуальное занятие', 0),
(15, 10, 'Групповое занятие', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `Property_String`
--

CREATE TABLE `Property_String` (
  `id` int(11) NOT NULL,
  `property_id` int(11) NOT NULL,
  `value` varchar(255) NOT NULL,
  `model_name` varchar(100) NOT NULL,
  `object_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `Property_String`
--

INSERT INTO `Property_String` (`id`, `property_id`, `value`, `model_name`, `object_id`) VALUES
(1, 9, 'https://vk.com/upstrocke', 'User', 95),
(2, 9, 'https://vk.com/id129018334', 'User', 101),
(3, 9, 'https://vk.com/id249437636', 'User', 103),
(4, 9, 'https://vk.com/id371845369', 'User', 108),
(5, 9, 'https://vk.com/id227312993', 'User', 112),
(6, 9, 'https://vk.com/id114953549', 'User', 113),
(7, 9, 'https://vk.com/sulzhenko22', 'User', 121),
(8, 9, 'https://vk.com/i9936955', 'User', 125),
(9, 9, 'https://vk.com/id147798154', 'User', 132),
(10, 9, 'https://vk.com/pingvin997', 'User', 133),
(11, 9, 'https://vk.com/zzzavarkina', 'User', 135),
(12, 9, 'https://vk.com/id100971552', 'User', 145),
(13, 9, 'https://vk.com/id35539072', 'User', 151),
(14, 9, 'https://vk.com/id321516053', 'User', 154),
(15, 9, 'https://vk.com/naty20213', 'User', 155),
(16, 9, 'https://vk.com/n.mass', 'User', 157),
(17, 9, 'https://vk.com/id_space_666', 'User', 159),
(18, 9, 'https://vk.com/irena_shilova', 'User', 161),
(19, 9, 'https://vk.com/mirdevshonok', 'User', 170),
(20, 9, 'https://vk.com/grustnayann', 'User', 171),
(21, 9, 'https://vk.com/donpad', 'User', 172),
(22, 9, 'https://vk.com/id139766263', 'User', 177),
(23, 9, 'https://vk.com/sergks', 'User', 178),
(24, 9, 'https://vk.com/id218411690', 'User', 181),
(25, 9, 'https://vk.com/id250401218', 'User', 195),
(26, 9, 'https://vk.com/id138219513', 'User', 197),
(27, 9, 'https://vk.com/a.paroeva', 'User', 200),
(28, 9, 'https://vk.com/matheuscesaro', 'User', 214),
(29, 9, 'https://vk.com/id52231178', 'User', 218),
(30, 9, 'https://vk.com/djon_djon', 'User', 222),
(31, 9, 'https://vk.com/w1n32', 'User', 258),
(32, 9, 'https://vk.com/lusineshahverdian', 'User', 259),
(33, 9, 'https://vk.com/dmitry_chablin', 'User', 270),
(34, 9, 'https://vk.com/mari_marsi', 'User', 276),
(35, 9, 'https://vk.com/viktoriya.bugaeva', 'User', 280),
(36, 9, 'https://vk.com/lyakh_mary', 'User', 285),
(37, 9, 'https://vk.com/ruzakin', 'User', 287),
(38, 9, 'https://vk.com/masteeer', 'User', 290),
(39, 9, 'https://vk.com/selse1', 'User', 295),
(40, 9, 'https://vk.com/id185797429', 'User', 297),
(41, 9, 'https://vk.com/angelina.statinova', 'User', 300),
(42, 9, 'https://vk.com/id7232566', 'User', 302),
(43, 9, 'https://vk.com/gudovilya', 'User', 311),
(44, 9, 'https://vk.com/academy31', 'User', 313),
(45, 9, 'https://vk.com/id38493200', 'User', 314),
(47, 9, 'https://vk.com/prisiazhnyuk_a', 'User', 322),
(48, 9, 'https://vk.com/ulysses2000', 'User', 325),
(49, 9, 'https://vk.com/just.vika', 'User', 334),
(51, 9, '', 'User', 98),
(52, 9, '', 'User', 96),
(53, 9, '', 'User', 97),
(54, 9, '', 'User', 100),
(55, 9, '', 'User', 102),
(56, 9, '', 'User', 104),
(57, 9, '', 'User', 105),
(58, 9, '', 'User', 106),
(59, 9, '', 'User', 107),
(60, 9, '', 'User', 109),
(61, 9, '', 'User', 110),
(62, 9, '', 'User', 111),
(63, 9, '', 'User', 114),
(64, 9, '', 'User', 291),
(65, 9, '', 'User', 46),
(66, 9, '', 'User', 42),
(67, 3, 'Параметр 1', 'Structure_Item', 1),
(68, 3, 'Параметр 2', 'Structure_Item', 1),
(69, 3, 'Параметр 3', 'Structure_Item', 2),
(70, 3, 'Параметр 4', 'Structure_Item', 2),
(71, 3, 'Параметр 5', 'Structure_Item', 2),
(72, 9, 'vk.com/id6846535', 'User', 99);

-- --------------------------------------------------------

--
-- Структура таблицы `Property_String_Assigment`
--

CREATE TABLE `Property_String_Assigment` (
  `id` int(11) NOT NULL,
  `property_id` int(11) NOT NULL,
  `object_id` int(11) NOT NULL,
  `model_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `Property_String_Assigment`
--

INSERT INTO `Property_String_Assigment` (`id`, `property_id`, `object_id`, `model_name`) VALUES
(1, 9, 42, 'User'),
(2, 9, 46, 'User'),
(3, 9, 83, 'User'),
(4, 9, 85, 'User'),
(5, 9, 86, 'User'),
(6, 9, 87, 'User'),
(7, 9, 88, 'User'),
(8, 9, 89, 'User'),
(9, 9, 90, 'User'),
(10, 9, 91, 'User'),
(11, 9, 92, 'User'),
(12, 9, 93, 'User'),
(13, 9, 94, 'User'),
(14, 9, 95, 'User'),
(15, 9, 96, 'User'),
(16, 9, 97, 'User'),
(17, 9, 98, 'User'),
(18, 9, 99, 'User'),
(19, 9, 100, 'User'),
(20, 9, 101, 'User'),
(21, 9, 102, 'User'),
(22, 9, 103, 'User'),
(23, 9, 104, 'User'),
(24, 9, 105, 'User'),
(25, 9, 106, 'User'),
(26, 9, 107, 'User'),
(27, 9, 108, 'User'),
(28, 9, 109, 'User'),
(29, 9, 110, 'User'),
(30, 9, 111, 'User'),
(31, 9, 112, 'User'),
(32, 9, 113, 'User'),
(33, 9, 114, 'User'),
(34, 9, 115, 'User'),
(35, 9, 116, 'User'),
(36, 9, 117, 'User'),
(37, 9, 118, 'User'),
(38, 9, 119, 'User'),
(39, 9, 120, 'User'),
(40, 9, 121, 'User'),
(41, 9, 122, 'User'),
(42, 9, 123, 'User'),
(43, 9, 124, 'User'),
(44, 9, 125, 'User'),
(45, 9, 126, 'User'),
(46, 9, 127, 'User'),
(47, 9, 128, 'User'),
(48, 9, 129, 'User'),
(49, 9, 130, 'User'),
(50, 9, 131, 'User'),
(51, 9, 132, 'User'),
(52, 9, 133, 'User'),
(53, 9, 134, 'User'),
(54, 9, 135, 'User'),
(55, 9, 136, 'User'),
(56, 9, 137, 'User'),
(57, 9, 138, 'User'),
(58, 9, 139, 'User'),
(59, 9, 140, 'User'),
(60, 9, 141, 'User'),
(61, 9, 142, 'User'),
(62, 9, 143, 'User'),
(63, 9, 144, 'User'),
(64, 9, 145, 'User'),
(65, 9, 146, 'User'),
(66, 9, 147, 'User'),
(67, 9, 148, 'User'),
(68, 9, 149, 'User'),
(69, 9, 150, 'User'),
(70, 9, 151, 'User'),
(71, 9, 152, 'User'),
(72, 9, 153, 'User'),
(73, 9, 154, 'User'),
(74, 9, 155, 'User'),
(75, 9, 156, 'User'),
(76, 9, 157, 'User'),
(77, 9, 158, 'User'),
(78, 9, 159, 'User'),
(79, 9, 160, 'User'),
(80, 9, 161, 'User'),
(81, 9, 162, 'User'),
(82, 9, 163, 'User'),
(83, 9, 164, 'User'),
(84, 9, 165, 'User'),
(85, 9, 166, 'User'),
(86, 9, 167, 'User'),
(87, 9, 168, 'User'),
(88, 9, 169, 'User'),
(89, 9, 170, 'User'),
(90, 9, 171, 'User'),
(91, 9, 172, 'User'),
(92, 9, 174, 'User'),
(93, 9, 175, 'User'),
(94, 9, 176, 'User'),
(95, 9, 177, 'User'),
(96, 9, 178, 'User'),
(97, 9, 179, 'User'),
(98, 9, 180, 'User'),
(99, 9, 181, 'User'),
(100, 9, 182, 'User'),
(101, 9, 183, 'User'),
(102, 9, 184, 'User'),
(103, 9, 185, 'User'),
(104, 9, 186, 'User'),
(105, 9, 187, 'User'),
(106, 9, 188, 'User'),
(107, 9, 189, 'User'),
(108, 9, 190, 'User'),
(109, 9, 191, 'User'),
(110, 9, 192, 'User'),
(111, 9, 193, 'User'),
(112, 9, 194, 'User'),
(113, 9, 195, 'User'),
(114, 9, 196, 'User'),
(115, 9, 197, 'User'),
(116, 9, 198, 'User'),
(117, 9, 199, 'User'),
(118, 9, 200, 'User'),
(119, 9, 201, 'User'),
(120, 9, 202, 'User'),
(121, 9, 203, 'User'),
(122, 9, 204, 'User'),
(123, 9, 205, 'User'),
(124, 9, 206, 'User'),
(125, 9, 207, 'User'),
(126, 9, 208, 'User'),
(127, 9, 209, 'User'),
(128, 9, 210, 'User'),
(129, 9, 211, 'User'),
(130, 9, 212, 'User'),
(131, 9, 213, 'User'),
(132, 9, 214, 'User'),
(133, 9, 215, 'User'),
(134, 9, 216, 'User'),
(135, 9, 217, 'User'),
(136, 9, 218, 'User'),
(137, 9, 219, 'User'),
(138, 9, 220, 'User'),
(139, 9, 221, 'User'),
(140, 9, 222, 'User'),
(141, 9, 223, 'User'),
(142, 9, 224, 'User'),
(143, 9, 225, 'User'),
(144, 9, 226, 'User'),
(145, 9, 227, 'User'),
(146, 9, 228, 'User'),
(147, 9, 229, 'User'),
(148, 9, 230, 'User'),
(149, 9, 232, 'User'),
(150, 9, 233, 'User'),
(151, 9, 234, 'User'),
(152, 9, 235, 'User'),
(153, 9, 236, 'User'),
(154, 9, 237, 'User'),
(155, 9, 238, 'User'),
(156, 9, 241, 'User'),
(157, 9, 242, 'User'),
(158, 9, 243, 'User'),
(159, 9, 244, 'User'),
(160, 9, 245, 'User'),
(161, 9, 246, 'User'),
(162, 9, 247, 'User'),
(163, 9, 248, 'User'),
(164, 9, 249, 'User'),
(165, 9, 250, 'User'),
(166, 9, 251, 'User'),
(167, 9, 252, 'User'),
(168, 9, 253, 'User'),
(169, 9, 254, 'User'),
(170, 9, 255, 'User'),
(171, 9, 256, 'User'),
(172, 9, 257, 'User'),
(173, 9, 258, 'User'),
(174, 9, 259, 'User'),
(175, 9, 260, 'User'),
(176, 9, 261, 'User'),
(177, 9, 262, 'User'),
(178, 9, 263, 'User'),
(179, 9, 264, 'User'),
(180, 9, 265, 'User'),
(181, 9, 266, 'User'),
(182, 9, 267, 'User'),
(183, 9, 268, 'User'),
(184, 9, 269, 'User'),
(185, 9, 270, 'User'),
(186, 9, 271, 'User'),
(187, 9, 272, 'User'),
(188, 9, 273, 'User'),
(189, 9, 274, 'User'),
(190, 9, 275, 'User'),
(191, 9, 276, 'User'),
(192, 9, 277, 'User'),
(193, 9, 278, 'User'),
(194, 9, 279, 'User'),
(195, 9, 280, 'User'),
(196, 9, 281, 'User'),
(197, 9, 282, 'User'),
(198, 9, 283, 'User'),
(199, 9, 284, 'User'),
(200, 9, 285, 'User'),
(201, 9, 286, 'User'),
(202, 9, 287, 'User'),
(203, 9, 288, 'User'),
(204, 9, 289, 'User'),
(205, 9, 290, 'User'),
(206, 9, 291, 'User'),
(207, 9, 292, 'User'),
(208, 9, 293, 'User'),
(209, 9, 294, 'User'),
(210, 9, 295, 'User'),
(211, 9, 296, 'User'),
(212, 9, 297, 'User'),
(213, 9, 298, 'User'),
(214, 9, 299, 'User'),
(215, 9, 300, 'User'),
(216, 9, 301, 'User'),
(217, 9, 302, 'User'),
(218, 9, 303, 'User'),
(219, 9, 304, 'User'),
(220, 9, 305, 'User'),
(221, 9, 306, 'User'),
(222, 9, 307, 'User'),
(223, 9, 308, 'User'),
(224, 9, 309, 'User'),
(225, 9, 310, 'User'),
(226, 9, 311, 'User'),
(227, 9, 312, 'User'),
(228, 9, 313, 'User'),
(229, 9, 314, 'User'),
(230, 9, 315, 'User'),
(231, 9, 316, 'User'),
(232, 9, 317, 'User'),
(233, 9, 318, 'User'),
(234, 9, 319, 'User'),
(235, 9, 320, 'User'),
(236, 9, 321, 'User'),
(237, 9, 322, 'User'),
(238, 9, 323, 'User'),
(239, 9, 324, 'User'),
(240, 9, 325, 'User'),
(241, 9, 326, 'User'),
(242, 9, 327, 'User'),
(243, 9, 328, 'User'),
(244, 9, 329, 'User'),
(245, 9, 330, 'User'),
(246, 9, 331, 'User'),
(247, 9, 332, 'User'),
(248, 9, 333, 'User'),
(249, 9, 334, 'User'),
(250, 9, 335, 'User'),
(251, 9, 336, 'User'),
(252, 3, 1, 'Structure_Item'),
(253, 3, 2, 'Structure_Item');

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
(1, 4, 'Описание футбольного мяча', 'Structure_Item', 1),
(2, 4, 'Описание кросовок 1', 'Structure_Item', 2),
(3, 4, 'Описание кросовок 2', 'Structure_Item', 2);

-- --------------------------------------------------------

--
-- Структура таблицы `Property_Text_Assigment`
--

CREATE TABLE `Property_Text_Assigment` (
  `id` int(11) NOT NULL,
  `property_id` int(11) NOT NULL,
  `object_id` int(11) NOT NULL,
  `model_name` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `Property_Text_Assigment`
--

INSERT INTO `Property_Text_Assigment` (`id`, `property_id`, `object_id`, `model_name`) VALUES
(1, 4, 1, 'Structure_Item'),
(2, 4, 2, 'Structure_Item');

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
  `active` int(11) NOT NULL DEFAULT '1',
  `sorting` int(11) NOT NULL,
  `meta_title` varchar(100) DEFAULT NULL,
  `meta_description` text,
  `meta_keywords` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `Structure`
--

INSERT INTO `Structure` (`id`, `title`, `parent_id`, `path`, `action`, `template_id`, `description`, `active`, `sorting`, `meta_title`, `meta_description`, `meta_keywords`) VALUES
(1, 'Магазин', 0, 'shop', 'catalog', 5, 'Интернет-магазин', 1, 0, '', '', ''),
(2, 'Спорт', 1, 'sport', 'catalog', 1, '', 1, 0, '', '', ''),
(3, 'Теннис', 2, 'tennis', 'catalog', 1, '', 1, 0, '', '', ''),
(4, 'Футбол', 2, 'football', 'catalog', 1, '', 1, 0, '', '', ''),
(5, 'Панель управления musadm', 0, '', 'musadm/index', 4, '', 1, 0, '', '', ''),
(6, 'Административный раздел', 0, 'admin', 'admin/index', 3, '', 1, 0, '', '', ''),
(7, 'Личный кабинет', 0, 'user', 'user', 4, '', 1, 0, '', '', ''),
(8, 'Иерархия классов', 9, 'models', 'documentation/models', 1, 'Общая структура системы. Описание стандартных классов, их свойств и методов.', 1, 0, '', '', ''),
(9, 'Документация', 0, 'documentation', 'documentation/index', 1, 'Руководство по использованию системы', 1, 0, '', '', ''),
(14, 'Авторизация', 5, 'authorize', 'musadm/authorize', 1, '', 1, 0, '', '', ''),
(15, 'Спорт2', 1, '', '', 0, '', 1, 0, NULL, NULL, NULL),
(16, 'Спорт3', 1, '', '', 0, '', 1, 0, NULL, NULL, NULL),
(17, 'Спорт4', 1, '', '', 0, '', 1, 0, NULL, NULL, NULL),
(18, 'Спорт5', 1, '', '', 0, '', 0, 0, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `Structure_Item`
--

CREATE TABLE `Structure_Item` (
  `id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `description` text,
  `active` int(11) NOT NULL DEFAULT '1',
  `meta_title` varchar(100) DEFAULT NULL,
  `meta_description` text,
  `meta_keywords` varchar(100) DEFAULT NULL,
  `path` varchar(50) NOT NULL,
  `sorting` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `Structure_Item`
--

INSERT INTO `Structure_Item` (`id`, `title`, `parent_id`, `description`, `active`, `meta_title`, `meta_description`, `meta_keywords`, `path`, `sorting`) VALUES
(1, 'Футбольный мячь', 4, '', 1, 'SEO title', 'SEO описание', 'SEO ключевые слова', '1', 0),
(2, 'Кросовки Nike', 4, '', 1, '', '', '', '2', 0),
(3, 'Название', 1, NULL, 1, NULL, NULL, NULL, '', 0),
(4, 'Название2', 1, NULL, 1, NULL, NULL, NULL, '', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `User`
--

CREATE TABLE `User` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `surname` varchar(50) NOT NULL,
  `patronimyc` varchar(50) NOT NULL,
  `phone_number` varchar(100) NOT NULL,
  `email` varchar(50) NOT NULL DEFAULT '',
  `login` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `group_id` int(11) NOT NULL DEFAULT '2',
  `register_date` date NOT NULL,
  `active` int(11) NOT NULL DEFAULT '1',
  `superuser` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `User`
--

INSERT INTO `User` (`id`, `name`, `surname`, `patronimyc`, `phone_number`, `email`, `login`, `password`, `group_id`, `register_date`, `active`, `superuser`) VALUES
(1, 'Егор', 'Козырев', 'Алексеевич', '8-980-378-28-56', 'creative27016@gmail.com', 'alexoufx', '4a7d1ed414474e4033ac29ccb8653d9b', 1, '0000-00-00', 1, 1),
(2, 'Имя', 'Фамилия', '', '8-980-888-88-88', 'test@email.ru', 'test', '098f6bcd4621d373cade4e832627b4f6', 2, '2018-02-15', 1, 0),
(42, 'Александр', 'Булгаков', '', '+79087801122', '', 'БА', 'd93591bdf7860e1e4ee2fca799911215', 4, '2018-03-20', 1, 0),
(46, 'Оксана', 'Полтева', '', '+79205764079', '', 'ПО', 'd93591bdf7860e1e4ee2fca799911215', 4, '2018-03-20', 1, 0),
(83, 'Алина', 'Романович', '', '+79192882062', '', 'РА', 'd93591bdf7860e1e4ee2fca799911215', 4, '2018-03-20', 1, 0),
(85, 'Артур', 'Герус', '', '30-18-77', '', 'd', '8277e0910d750195b448797616e091ad', 3, '2018-03-20', 1, 0),
(86, 'Дарья', 'Черных', '', '+79155665673', '', 'ЧД', 'd93591bdf7860e1e4ee2fca799911215', 4, '2018-03-20', 1, 0),
(87, 'Руслан', 'Галяутдинов', '', '+79202090014', '', 'ГР', 'd93591bdf7860e1e4ee2fca799911215', 4, '2018-03-20', 1, 0),
(88, 'Карина', 'Белякова', '', '+79155601114', '', 'БК', 'd93591bdf7860e1e4ee2fca799911215', 4, '2018-03-20', 1, 0),
(89, 'Владимир', 'Варжавинов', '', '+79606336651', '', 'ВВ', 'd93591bdf7860e1e4ee2fca799911215', 4, '2018-03-20', 1, 0),
(90, 'Вадим', 'Фёдоров', '', '+79092019737', '', 'ФВ', 'd93591bdf7860e1e4ee2fca799911215', 4, '2018-03-20', 1, 0),
(91, 'Андрей', 'Матвиенко', '', '+79045311244', '', 'МА', 'd93591bdf7860e1e4ee2fca799911215', 4, '2018-03-20', 1, 0),
(92, 'Андрей', 'Жуйков', '', '+79040872458', '', 'ЖА', 'd93591bdf7860e1e4ee2fca799911215', 4, '2018-03-20', 1, 0),
(93, 'Айарпи', 'Джилавян', '', '+79606371162', '', 'ДА', 'd93591bdf7860e1e4ee2fca799911215', 4, '2018-03-20', 1, 0),
(94, 'Иван', 'Рымарев', '', '+79040904607', '', 'РИ', 'd93591bdf7860e1e4ee2fca799911215', 4, '2018-03-20', 1, 0),
(95, 'Максим', 'Рябых ', '', '+79611640794', '', 'РяМа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(96, 'Евгений', 'Байдиков', '', '9511340707', '', 'БаЕв', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(97, 'Дмитрий', 'Тетюхин', '', '9107419097, мама9103295637', '', 'ТеДи', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(98, 'Оксана', 'Захарова', '', '9511406093', '', 'ЗаОк', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(99, 'Чавес', 'Дебора', '', '9205967925', '', 'ДеЧа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(100, 'Михаил', 'Диченко', '', '9040822225', '', 'ДиМи', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0),
(101, 'Дарья', 'Ведерникова', '', '9192884237', '', 'ВеДа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(102, 'Юлия', 'Савенкова', '', '9606323377', '', 'СаЮл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(103, 'Юлия', 'Дворецкая', '', '9155636799', '', 'ДвЮл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(104, 'Михаил', 'Кравцов', '', '9102237815, мама 9155274452', '', 'КрМи', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(105, 'Ангелина', 'Тараник', '', '9803753021 папа', '', 'ТаАн', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(106, 'Мария', 'Котова', '', '9192213076', '', 'КоПо', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(107, 'Аврора', 'Мочалова', '', '9202002087', '', 'МоАв', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(108, 'Карина', 'Чумак', '', '9092020721', '', 'ЧуКа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(109, 'Дмитрий', 'Сидлецкий', '', '9205792055', '', 'СиДм', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(110, 'Денис', 'Егоров', '', '9155730675', '', 'ЕгДе', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(111, 'Варвара', 'Худасова', '', '9087814060', '', 'ХуВа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0),
(112, 'Ирина', 'Васильченко', '', '9092055444', '', 'ВаИр', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(113, 'Даниил', 'Немыкин', '', '9065654967', '', 'НеДа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(114, 'Андрей', 'Кузьмичев', '', '9040908554, мама9511592054', '', 'КуАн', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(115, 'Ксения', 'Игнатова', '', '9040941155', '', 'ИгКс', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(116, 'Мария', 'Косухина', '', '9202065482, 9192850039', '', 'КоМа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(117, 'Марина', 'Орлова', '', '9066005833', '', 'ОрМа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0),
(118, 'Алиса', 'Дорохова', '', '9155750794', '', 'ДоАл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(119, 'Константин', 'Баландин', '', '9524349900', '', 'БаКо', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(120, 'Анна', 'Корнеева', '', '9805291851', '', 'КоАн', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0),
(121, 'Светлана', 'Лукашова', '', '9511469962', '', 'ЛуСв', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(122, 'София', 'Корнеева ', '', '9805291851', '', 'КоСо', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0),
(123, 'Диляра', 'Новак', '', '9606404124', '', 'НоДи', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(124, 'Лия', 'Склярова', '', '9092094948', '', 'СкЛи', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0),
(125, 'Елизавета', 'Потатушкина', '', '9155764370', '', 'ПоЕл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(126, 'Евгения', 'Цветкова', '', '9611777964', '', 'ЦвЕв', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0),
(127, 'Алина', 'Глебова', '', '9805253750', '', 'ГлАн', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(128, 'Алексей', 'Горошко', '', '9192822866', '', 'ГоАл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(129, 'Виолетта', 'Корсакова', '', 'мама9192250899, 9194365532', '', 'КоВи', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(130, 'Екатерина', 'Лозовская', '', '9606753635', '', 'ЛоЕк', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0),
(131, 'Наталья', 'Павлик', '', '9066048283', '', 'ПаНа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0),
(132, 'Анастасия', 'Арчибасова', '', '9606262562', '', 'АрАн', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(133, 'Виктория', 'Думанова', '', '9205665205', '', 'ДуВи', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(134, 'Ольга', 'Малькова', '', '9102258020', '', 'МаОл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(135, 'Алёна', 'Баймакова', '', '9092043065', '', 'БаАл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(136, 'Алиса', 'Лылова', '', '', '', 'ЛыАл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(137, 'Константин', 'Клёсов', '', '9040816850', '', 'КлКо', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(138, 'Анна', 'Лылова', '', '', '', 'ЛыАн', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(139, 'Владимир', 'Кузнецов', '', '9205750117', '', 'КуВл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0),
(140, 'Владислав', 'Егоров ', '', '9155730675', '', 'ЕгВл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(141, 'Анастасия', 'Жарикова', '', '9087820943', '', 'ЖаАн', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(142, 'София', 'Курбатова', '', '9087870319', '', 'КуСо', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0),
(143, 'Анастасия', 'Пыркина', '', '9056737739', '', 'ПыАн', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(144, 'Оксана', 'Зиневич', '', '9194374668', '', 'ЗиОк', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(145, 'Екатерина', 'Кобзева', '', '9524331330', '', 'КоЕк', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(146, 'Кирилл', 'Катаржнов', '', '9517624136', '', 'КаКи', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0),
(147, 'Жаломба', 'Селештино', '', '9155297143', '', 'СеЖа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(148, 'Ася', 'Дугнист', '', '9087867792', '', 'ДуАс', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(149, 'Кирилл', 'Пивнев', '', '9524399874', '', 'ПиКи', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(150, 'Ангелина', 'Клочева', '', '9205815530', '', 'КлАн', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0),
(151, 'Кристина', 'Гудкова', '', '9995190691', '', 'ГуКр', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(152, 'Елена', 'Сухобрус', '', '9036424424', '', 'СуЕл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0),
(153, 'Мурадлы', 'Фидан', '', 'мама9524205114, 9803261145', '', 'ФиМу', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0),
(154, 'Марина', 'Ерофеева', '', '9205896526', '', 'ЕрМа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(155, 'Наталья', 'Васильева', '', '9192877530', '', 'ВаНа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(156, 'Юлианна', 'Пронина', '', '9202653722', '', 'ПрЮл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0),
(157, 'Наталья', 'Масс', '', '9606267288', '', 'МаНа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(158, 'Светлана', 'Исаева', '', '9102264633', '', 'ИсСв', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(159, 'Анатолий', 'Самокиша', '', '9205768223, папа9623082588', '', 'СаАн', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(160, 'Светлана', 'Саак', '', '9202001717', '', 'СаСв', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(161, 'Ирина', 'Шилова', '', '9102288507', '', 'ШиИр', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(162, 'Алина', 'Малькова', '', '9040862858', '', 'МаАл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(163, 'Оксана', 'Гринько', '', '9103263030', '', 'ГрОк', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(164, 'Владислав', 'Цунаев', '', '9803255558', '', 'ЦуВл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0),
(165, 'Екатерина', 'Швецова', '', '9087811417', '', 'ШвЕк', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(166, 'Яна', 'Акиньшина', '', '89511357177', '', 'АкЯн', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(167, 'Юлия', 'Фролова', '', 'мама9611737216, 9606305470', '', 'ФрЮл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(168, 'Алина', 'Гунько', '', '9103611195', '', 'ГуАл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(169, 'Ольга', 'Славная', '', '9040877121', '', 'СлОл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0),
(170, 'Юлия', 'Довбыш', '', '9066004550', '', 'ДоЮл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(171, 'Анна', 'Ермолина', '', '9092035014', '', 'ЕрАн', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(172, 'Сергей', 'Миронов', '', '9155257009 9087897257, мама 9803242721', '', 'МиСе', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(174, 'Маргарита', 'Кононова', '', '9194375899', '', 'КонМа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(175, 'Ксения', 'Миженина', '', '9107372122', '', 'МиКс', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(176, 'Дарья', 'Еремина', '', '9087811270', '', 'ЕрДа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(177, 'Анна', 'Арчибасова', '', '', '', 'АрчАн', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(178, 'Сергей', 'Картамышев', '', '9045334421', '', 'КаСе', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(179, 'Екатерина', 'Лаврова', '', 'анкет9045307762, осн9040883998 ', '', 'ЛаЕк', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0),
(180, 'Алексей', 'Евтушенко', '', '9087826599', '', 'ЕвАл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0),
(181, 'Вадим', 'Баранов', '', '9155271114', '', 'БаВа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(182, 'Дмитрий', 'Тагиев', '', 'мама9045369383, 9080806456', '', 'ТаДм', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(183, 'Юлия', 'Дель', '', '9805269096', '', 'ДеЮл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0),
(184, 'Владислав', 'Краснов', '', '9202012313', '', 'КрВл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0),
(185, 'Ирина', 'Пуляева', '', '9205825195, 9384065884', '', 'ПуИр', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0),
(186, 'Владислав', 'Шейченко', '', '9611704948, мама9036421971', '', 'ШеВл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(187, 'Анастасия', 'Зубарева', '', '', '', 'ЗуАн', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(188, 'Радмила', 'Мартынова', '', '9803772116, 9107414966', '', 'МаРа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(189, 'Ольга', 'Сырвачева', '', '9087890517', '', 'СыОл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0),
(190, 'Вадим', 'Ляпин', '', '9155688019мама', '', 'ЛяВа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(191, 'Дарья', 'Сидорова', '', '9511326444', '', 'СиДа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(192, 'Екатерина', 'Раушенбах', '', '', '', 'РаЕк', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0),
(193, 'Павел', 'Сайганов', '', '+79290028122, +79040859023', '', 'СП', 'd93591bdf7860e1e4ee2fca799911215', 4, '2018-03-20', 1, 0),
(194, 'Марина', 'Резникова', '', '9304000564', '', 'РеМа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0),
(195, 'Егор', 'Попов', '', '9194307480, 9155783090', '', 'ПоЕг', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(196, 'Римма', 'Ремизова', '', '9202068712', '', 'РеРи', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(197, 'Маргарита', 'Ремизова', '', '9202044725', '', 'РемМа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(198, 'Игорь', 'Самойлов', '', '9038845902', '', 'СаИг', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0),
(199, 'Елена', 'Сидоренко', '', '9192870280', '', 'СиЕл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(200, 'Анастасия', 'Сидорова', '', '9155750240', '', 'СиАн', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(201, 'Дмитрий', 'Сорокин', '', '9611728486', '', 'СоДм', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(202, 'Ольга', 'Тодощук', '', '9205545813', '', 'ТоОл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0),
(203, 'Егор', 'Тулинов', '', 'мама9065668548', '', 'ТуЕг', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(204, 'Александр', 'Токач', '', 'мама9103221771, 9103641033', '', 'ТоАл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(205, 'Наталья', 'Цыбенко', '', '9102206629', '', 'ЦыНа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0),
(206, 'Марина', 'Цыкал', '', '9277440137, 9205921682', '', 'ЦыМа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(207, 'Алина', 'Ширшова', '', '9645813172, 9045300028', '', 'ШиАл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(208, 'Вадим', 'Пелихов', '', '9205626488', '', 'ПеВа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(209, 'Михаил', 'Лагуткин', '', '9524398242', '', 'ЛаМи', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(210, 'Валентин', 'Слюсарев', '', '9040909913', '', 'СлВа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(211, 'Аким', 'Дмитриев', '', '9511411474', '', 'ДмАк', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(212, 'Валерий', 'Нерсисян', '', '9803738628', '', 'НеВа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0),
(213, 'Григорий', 'Орлов', '', '9103609153', '', 'ОрГр', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(214, 'Санчес', 'Матеус', '', '9205967925', '', 'МаСа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(215, 'Владислав', 'Литвинов', '', '9205559706, мама 9205843920', '', 'ЛиВл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0),
(216, 'Илья', 'Наумов', '', '9202046538', '', 'НаИл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(217, 'Дарина', 'Леонова', '', '9202009909', '', 'ЛеДа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0),
(218, 'Алиса', 'Кудрявцева', '', '9517600055', '', 'КуАл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(219, 'Александр', 'Кравченко', '', '9205556546', '', 'КрАл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0),
(220, 'Кирилл', 'Гнаповский', '', '9103667703', '', 'ГнКи', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(221, 'Дарья', 'Остапенко', '', '9202079686, мама 9524312072', '', 'ОсДа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0),
(222, 'Василий', 'Девятов', '', '9102267886, 9205699500', '', 'ДеВа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(223, 'Ярослав', 'Истрашкин', '', '9517692499мама', '', 'ИсЯр', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(224, 'Арсений', 'Коренев', '', 'водитель 9103206069', '', 'КоАр', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(225, 'Дмитрий', 'Климов', '', '9511525097', '', 'КлДм', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(226, 'Ульяна', 'Егорова', '', '9155716692', '', 'ЕгУл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(227, 'Дарья', 'Дадыка', '', 'вк', '', 'ДаДа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0),
(228, 'Александр', 'Гамза', '', '9125544369', '', 'ГаАл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0),
(229, 'Валерия', 'Гончарова', '', '9155254048', '', 'ГоВа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(230, 'Павел', 'Шуленин', '', 'мама9205698623, 9205733776', '', 'ШуПа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(232, 'Валерий', 'Бескровный', '', '9524231323', '', 'БеВа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(233, 'Татьяна', 'Орлова', '', '9056704351', '', 'ОрТа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0),
(234, 'Вера', 'Мартынюк', '', '9045303768папа 89040929827', '', 'МаВе', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(235, 'Ирина', 'Овсиенко', '', '9517613513', '', 'ОвИр', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0),
(236, 'Дмитрий', 'Бутенко', '', '9205541023', '', 'БуДм', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0),
(237, 'Артём', 'Серёгин', '', '', '', 'СеАр', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0),
(238, 'Иван', 'Дементьев', '', '9205925424', '', 'ДеИв', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0),
(241, 'Вячеслав', 'Раков', '', '+79107366746', '', 'РаВя', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(242, 'Михаил', 'Михайленко', '', '+79087801127', '', 'МиМи', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(243, 'Светлана', 'Свидовская', '', '9511363580', '', 'СвСв', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0),
(244, 'Станислав', 'Звонов', '', '9803711540, 9066009119', '', 'ЗвСт', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(245, 'Егор', 'Крючков', '', '9202056051', '', 'КрЕг', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(246, 'Виталий', 'Макеев', '', '9065663579', '', 'МВ', 'd93591bdf7860e1e4ee2fca799911215', 4, '2018-03-20', 1, 0),
(247, 'Герман', 'Семёнов', '', '9102204713', '', 'СеГе', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(248, 'Ирина', 'Бойченко', '', '9148554126', '', 'БоИр', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0),
(249, 'Андрей', 'Ракитин', '', '9205720150', '', 'РаАн', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0),
(250, 'Дмитрий', 'Лебеденко', '', '9066059995', '', 'ЛеДм', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0),
(251, 'Денис', 'Гнездилов', '', '9192243506', '', 'ГнДе', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(252, 'София', 'Полтева', '', '', '', 'ПолСо', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(253, 'Оксана', 'Полтева', '', '', '', 'ПолОк', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(254, 'сультация', 'кон', '', '', '', 'консул', '9ab436ca9482f1cfcaee53cc1a6ab7c1', 5, '2018-03-20', 1, 0),
(255, 'Илья', 'Солдаткин ', '', '9803886673', '', 'СоИл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(256, 'Ярослав', 'Лебеденко', '', '9038860555', '', 'ЛеЯр', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(257, 'Виктория', 'Будько', '', '9087828685', '', 'БуВи', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(258, 'Игорь', 'Качук', '', '9030246755', '', 'КаИг', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(259, 'Лусине', 'Шахвердян', '', '9102287787', '', 'ШаЛу', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(260, 'Вадим', 'Юдин', '', '9103231177', '', 'ЮдВа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0),
(261, 'Елена', 'Щетинина', '', '9036420233', '', 'ЩеЕл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(262, 'Подготовка', 'Само', '', '', '', 'Самопод', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(263, 'Маргарита', 'Харисова', '', '89155799809', '', 'ХаМа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(264, 'Алексей', 'Лагутин ', '', '89192221177', '', 'ЛаАл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(265, 'Елизавета', 'Лагутина', '', '', '', 'ЛаЕл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(266, 'Тест', 'Тест', '', '', '', 'тест', '81dc9bdb52d04dc20036dbd8313ed055', 4, '2018-03-20', 1, 0),
(267, 'Андрей', 'Кисенко', '', '89606326740', '', 'КиАн', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(268, 'Ярослав', 'Гринякин', '', '89805211205мама 89087845511', '', 'ГрЯр', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(269, 'Максим', 'Москаленко', '', '8-951-766-02-77', '', 'МоМа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(270, 'Дмитрий', 'Чаблин', '', '', '', 'ЧаДм', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0),
(271, 'Максим', 'Перепелица', '', '9155600680', '', 'ПеМа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(272, 'Александр', 'Щипцов', '', '9202003900, папа9250429770', '', 'ЩиАл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(273, 'Подготовка', 'Само', '', '', '', 'Само', 'efbab39a1edd45d202bb5add9a9df753', 4, '2018-03-20', 1, 0),
(274, 'Павел', 'Логвинов', '', '89192871619 мама', '', 'НоПа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(275, 'Анна', 'Зинькова', '', '9102251201', '', 'ЗиАн', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(276, 'Марина', 'Субботина ', '', '89205536278', '', 'СуМа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(277, 'Виктория', 'Рвачева ', '', 'мама89194324808, папа9290000886, 9155742281', '', 'РвВи', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(278, 'Евгений', 'Петровский ', '', '89103244086 мама', '', 'ПеЕв', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(279, 'Лариса', 'Зырянова', '', '89045349415', '', 'ЗыЛа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0),
(280, 'Виктория', 'Бугаева', '', ' 89103275764', '', 'БугВи', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(281, 'Владислав', 'Хрипунов', '', '89107451425', '', 'ХрВл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0),
(282, 'Наталья', 'Глущенко ', '', '89507136447', '', 'ГлНа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(283, 'Артем', 'Ломакович', '', '89606254054мама', '', 'ЛоАр', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(284, 'Даниил', 'Матвеенко ', '', '89611727212', '', 'МаДа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0),
(285, 'Мария', 'Рамазанова', '', '89103698305', '', 'РаМа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(286, 'Владислав', 'Гобелко', '', '9511594339', '', 'ГоВл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(287, 'Никита', 'Рузакин', '', '9511596862', '', 'РуНи', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0),
(288, 'София', 'Степаненко', '', '9511581710мама, 9511582956бабушка', '', 'СтСо', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(289, 'Юлия', 'Белкина', '', '9803740377', '', 'БеЮл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0),
(290, 'Татьяна', 'Камнева', '', '9040918558', '', 'КаТа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(291, 'Степан', 'Лисуненко', '', '9066083197, 9092006939', '', 'ЛиСт', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(292, 'Дарья', 'Курдюкова', '', '9103249006', '', 'КуДа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(293, 'Вероника', 'Варёшина', '', '89803756977', '', 'ВаВе', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0),
(294, 'Татьяна', 'Шмелева', '', '9103269964', '', 'ШмТа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(295, 'Алексей', 'Бочаров', '', '', '', 'БоАл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0),
(296, 'Вадим', 'Тихонов', '', '89038845555', '', 'ТиВа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(297, 'Евгения', 'Тулупова', '', '89066084042мама,  89606368035', '', 'ТуЕв', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(298, 'Артём', 'Филатов', '', '9103658036', '', 'ФиАр', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(299, 'Ольга', 'Гоменнюк ', '', '89202066147', '', 'ГоОл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0),
(300, 'Ангелина', 'Статинова', '', '9507179331', '', 'СтАн', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(301, 'Галина', 'Салтыкова ', '', '89155255941', '', 'СаГа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(302, 'Павел', 'Харченко', '', '89192809111 89155255941', '', 'ХаПа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(303, 'Анастасия', 'Федорова ', '', '9065668812', '', 'ФеАн', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0),
(304, 'Валерия', 'Журакова', '', 'валерия9517665598, 9040985488, 9507129046', '', 'ЖуВа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(305, 'Виолетта', 'Шапошникова', '', '9103207761мама', '', 'ШаВи', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(306, 'Александра', 'Мочалова', '', '', '', 'МоАл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(307, 'Владислава', 'Котельникова', '', '9066085215', '', 'КоВл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(308, 'Павел', 'Котельников', '', '9066085215', '', 'КоПа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(309, 'Виталий', 'Чепурных', '', '9040857580', '', 'ЧеВи', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(310, 'Станислав', 'Лукаш', '', '89092029210', '', 'СтЛу', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(311, 'Илья', 'Гудов', '', '9092018952', '', 'ГуИл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(312, 'Ирина', 'Витковская', '', '9155646210', '', 'ВиИр', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(313, 'Карина', 'Власенко', '', '89194322588', '', 'ВлКа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(314, 'Николай', 'Бурлаков', '', '9205853425', '', 'БуНи', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(315, 'Руслан', 'Исмаинов', '', '9103615681', '', 'ИсРу', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(316, 'Эльвира', 'Алексеенко', '', '9205592799', '', 'АлЭл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(317, 'Никита', 'Калинский', '', '89103262850', '', 'КаНи', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(318, 'Алина', 'Смирнова', '', '89803826884', '', 'СмАл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(319, 'Евгения', 'Шеховонова ', '', '89051733534', '', 'ШеЕв', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(320, 'Галина', 'Синегубова ', '', '89045305856', '', 'СиГа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(321, 'Марина', 'Овсянникова', '', '89192293607', '', 'ОвМа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(322, 'Анастасия', 'Присяжнюк', '', '89524319720', '', 'ПрАн', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(323, 'Полина', 'Котова', '', 'мама9045300066, 9045307959', '', 'КотПо', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(324, 'Тимофей', 'Воронцов', '', '9155627706', '', 'ВоТи', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0),
(325, 'Лилия', 'Сапельник', '', '', '', 'СаЛи', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(326, 'Илья', 'Каленик', '', '89155799510', '', 'КаИл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(327, 'Виолетта', 'Яркова', '', '89087805623', '', 'ЯрВи', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(328, 'Владимир', 'Дуюн', '', '9040882772', '', 'ДуВл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(329, 'Евгений', 'Лобанов', '', '9606300001', '', 'ЛоЕв', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(330, 'Татьяна', 'Рябухина', '', '9103623751, папа9040962985', '', 'РяТа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(331, 'Анастасия', 'Петкевич', '', '9205968524', '', 'ПеАн', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(332, 'Людмила', 'Романова', '', '9066068213', '', 'РоЛю', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(333, 'Лариса', 'Малявина', '', '', '', 'МаЛа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(334, 'Виктория', 'Глебова', '', '89040890974', '', 'ГлВи', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(335, 'Галина', 'Кривопускова', '', '89205715555', '', 'КрГа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(336, 'Иван', 'Козицкий', '', '9103690506мама, 9103690702', '', 'КоИв', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0),
(338, 'Олег', 'Галицин', 'Владимирович', '8-800-555-35-35', '', 'oleg', '4a7d1ed414474e4033ac29ccb8653d9b', 2, '2018-03-21', 1, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `User_Group`
--

CREATE TABLE `User_Group` (
  `id` int(11) NOT NULL,
  `title` varchar(50) NOT NULL,
  `sorting` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `User_Group`
--

INSERT INTO `User_Group` (`id`, `title`, `sorting`) VALUES
(1, 'Администратор', 0),
(2, 'Пользователь', 10),
(3, 'Директор', 20),
(4, 'Учитель', 30),
(5, 'Студент', 40);

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
-- Индексы таблицы `Property_Int_Assigment`
--
ALTER TABLE `Property_Int_Assigment`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `Property_List`
--
ALTER TABLE `Property_List`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `Property_List_Assigment`
--
ALTER TABLE `Property_List_Assigment`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `Property_List_Values`
--
ALTER TABLE `Property_List_Values`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `Property_String`
--
ALTER TABLE `Property_String`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `Property_String_Assigment`
--
ALTER TABLE `Property_String_Assigment`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `Property_Text`
--
ALTER TABLE `Property_Text`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `Property_Text_Assigment`
--
ALTER TABLE `Property_Text_Assigment`
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;
--
-- AUTO_INCREMENT для таблицы `Admin_Form_Modelname`
--
ALTER TABLE `Admin_Form_Modelname`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
--
-- AUTO_INCREMENT для таблицы `Admin_Form_Type`
--
ALTER TABLE `Admin_Form_Type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT для таблицы `Admin_Menu`
--
ALTER TABLE `Admin_Menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
--
-- AUTO_INCREMENT для таблицы `Constant`
--
ALTER TABLE `Constant`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT для таблицы `Constant_Dir`
--
ALTER TABLE `Constant_Dir`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT для таблицы `Constant_Type`
--
ALTER TABLE `Constant_Type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT для таблицы `Page_Template`
--
ALTER TABLE `Page_Template`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT для таблицы `Page_Template_Dir`
--
ALTER TABLE `Page_Template_Dir`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `Property`
--
ALTER TABLE `Property`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT для таблицы `Property_Dir`
--
ALTER TABLE `Property_Dir`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT для таблицы `Property_Int`
--
ALTER TABLE `Property_Int`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=437;
--
-- AUTO_INCREMENT для таблицы `Property_Int_Assigment`
--
ALTER TABLE `Property_Int_Assigment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT для таблицы `Property_List`
--
ALTER TABLE `Property_List`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT для таблицы `Property_List_Assigment`
--
ALTER TABLE `Property_List_Assigment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT для таблицы `Property_List_Values`
--
ALTER TABLE `Property_List_Values`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
--
-- AUTO_INCREMENT для таблицы `Property_String`
--
ALTER TABLE `Property_String`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;
--
-- AUTO_INCREMENT для таблицы `Property_String_Assigment`
--
ALTER TABLE `Property_String_Assigment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=254;
--
-- AUTO_INCREMENT для таблицы `Property_Text`
--
ALTER TABLE `Property_Text`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT для таблицы `Property_Text_Assigment`
--
ALTER TABLE `Property_Text_Assigment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT для таблицы `Structure`
--
ALTER TABLE `Structure`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
--
-- AUTO_INCREMENT для таблицы `Structure_Item`
--
ALTER TABLE `Structure_Item`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT для таблицы `User`
--
ALTER TABLE `User`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=339;
--
-- AUTO_INCREMENT для таблицы `User_Group`
--
ALTER TABLE `User_Group`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
