-- phpMyAdmin SQL Dump
-- version 4.6.5.2
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Апр 04 2018 г., 16:39
-- Версия сервера: 5.7.16
-- Версия PHP: 5.6.29

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
  `required` int(11) NOT NULL,
  `sorting` int(11) NOT NULL,
  `list_name` varchar(50) NOT NULL,
  `value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `Admin_Form`
--

INSERT INTO `Admin_Form` (`id`, `model_id`, `title`, `var_name`, `maxlength`, `type_id`, `active`, `required`, `sorting`, `list_name`, `value`) VALUES
(1, 1, 'Заголовок', 'title', 150, 2, 1, 0, 1, '', ''),
(2, 1, 'Путь', 'path', 100, 2, 1, 0, 2, '', ''),
(3, 1, 'Активость', 'active', 0, 3, 1, 0, 3, '', ''),
(4, 1, 'Файл обработчик', 'action', 100, 2, 1, 0, 4, '', ''),
(5, 1, 'Родительский раздел', 'parentId', 0, 4, 1, 0, 5, 'Structures', ''),
(6, 1, 'Макет', 'template_id', 0, 4, 1, 0, 6, 'Templates', ''),
(7, 1, 'Описание', 'description', 2000, 5, 1, 0, 7, '', ''),
(8, 2, 'Название', 'title', 150, 2, 1, 0, 1, '', ''),
(9, 2, 'Путь', 'path', 50, 2, 1, 0, 2, '', ''),
(10, 1, 'Сортировка', 'sorting', 0, 1, 1, 0, 8, '', ''),
(12, 2, 'Сортировка', 'sorting', 0, 1, 1, 0, 4, '', ''),
(13, 2, 'Активность', 'active', 0, 3, 1, 0, 3, '', ''),
(14, 2, 'Родительский раздел', 'parentId', 0, 4, 1, 0, 3, 'Structures', ''),
(15, 3, 'Заголовок', 'title', 150, 2, 1, 0, 1, '', ''),
(16, 3, 'Название константы (в верхнем регистре)', 'name', 150, 2, 1, 1, 2, '', ''),
(17, 3, 'Описание', 'description', 2000, 5, 1, 0, 3, '', ''),
(19, 3, 'Значение', 'value', 2000, 5, 1, 0, 4, '', ''),
(20, 3, 'Тип значения', 'valueType', 0, 4, 1, 0, 5, 'ConstantTypes', ''),
(21, 3, 'Активность', 'active', 0, 3, 1, 0, 3, '', ''),
(22, 3, 'Родительская директория', 'dir', 0, 4, 1, 0, 4, 'ConstantDirs', ''),
(23, 4, 'Заголовок', 'title', 150, 2, 1, 0, 1, '', ''),
(24, 4, 'Описание', 'description', 2000, 5, 1, 0, 2, '', ''),
(25, 4, 'Родительский раздел', 'parentId', 0, 4, 1, 0, 3, 'ConstantDirs', ''),
(26, 4, 'Сортировка', 'sorting', 0, 1, 1, 0, 4, '', ''),
(27, 6, 'Название группы', 'title', 50, 2, 1, 0, 0, '', ''),
(28, 6, 'Сортировка', 'sorting', 0, 1, 1, 0, 0, '', ''),
(29, 5, 'Имя', 'name', 50, 2, 1, 0, 0, '', ''),
(30, 5, 'Фамилия', 'surname', 50, 2, 1, 0, 10, '', ''),
(31, 5, 'Отчество', 'patronimyc', 50, 2, 1, 0, 20, '', ''),
(32, 5, 'Номер телефона', 'phoneNumber', 100, 2, 1, 0, 30, '', ''),
(33, 5, 'Логин', 'login', 50, 2, 1, 0, 40, '', ''),
(34, 5, 'Пароль', 'pass1', 50, 6, 1, 0, 50, '', ''),
(35, 5, 'Повторите пароль', 'pass2', 50, 6, 1, 0, 60, '', ''),
(36, 7, 'Название модели', 'model_name', 255, 2, 1, 0, 20, '', ''),
(37, 7, 'Заголовок', 'model_title', 150, 2, 1, 0, 10, '', ''),
(38, 7, 'Сортировка', 'model_sorting', 0, 1, 1, 0, 30, '', ''),
(39, 10, 'Заголовок', 'title', 150, 2, 1, 0, 10, '', ''),
(40, 10, 'Название переменной (сеттера)', 'varName', 50, 2, 1, 0, 20, '', ''),
(41, 10, 'Родительская модель', 'model_id', 0, 4, 1, 0, 30, 'AdminFormModelnames', ''),
(42, 10, 'Максимальная длинна поля', 'maxlength', 0, 1, 1, 0, 40, '', ''),
(43, 10, 'Тип поля', 'type_id', 0, 4, 1, 0, 50, 'AdminFormTypes', ''),
(44, 10, 'Активность', 'active', 0, 3, 1, 0, 60, '', ''),
(45, 10, 'Сортировка', 'sorting', 0, 1, 1, 0, 70, '', ''),
(46, 10, 'Название списка', 'listName', 50, 2, 1, 0, 80, '', ''),
(47, 10, 'Значение', 'value', 2000, 5, 1, 0, 90, '', ''),
(49, 10, 'Обязательное поле', 'required', 0, 3, 1, 0, 100, '', '');

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
(10, 'Admin_Form', '', 0, 0);

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
  `title` varchar(50) NOT NULL,
  `model` varchar(50) NOT NULL,
  `sorting` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `Admin_Menu`
--

INSERT INTO `Admin_Menu` (`id`, `title`, `model`, `sorting`) VALUES
(1, 'Главная', 'Main', 0),
(2, 'Структуры', 'Structure', 10),
(3, 'Пользователи', 'User', 40),
(4, 'Константы', 'Constant', 30),
(5, 'Формы редактирования', 'Form', 50),
(6, 'Макеты', 'Template', 20);

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
(4, 'Пагинация в админ. разделе', 'SHOW_LIMIT', 'Лимит показов количества структур и объектов структур', '5', 1, 1, 1, 0),
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
  `dir` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `Property`
--

INSERT INTO `Property` (`id`, `tag_name`, `title`, `description`, `type`, `active`, `dir`) VALUES
(1, 'price', 'Цена', '', 'list', 1, 0),
(2, 'count', 'Количество', '', 'int', 1, 0),
(3, 'params', 'Параметры', '', 'text', 1, 0),
(4, 'description', 'Описание', '', 'text', 1, 0),
(5, 'list', 'Тестовый список', '', 'list', 1, 0),
(6, 'test', 'Тестовой свойство', '', 'int', 1, 0),
(7, 'tarif', 'Тариф', '', 'list', 1, 0),
(8, 'age', 'Возраст', '', 'int', 1, 0),
(9, 'vk', 'Ссылка вконтакте', '', 'string', 1, 0),
(10, 'lesson_type', 'Тип урока', '', 'list', 1, 0);

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
(9, 8, 24, 'User', 42),
(10, 8, 17, 'User', 46),
(11, 8, 0, 'User', 83),
(12, 8, 0, 'User', 85),
(13, 8, 0, 'User', 86),
(14, 8, 0, 'User', 87),
(15, 8, 0, 'User', 88),
(16, 8, 0, 'User', 89),
(17, 8, 0, 'User', 90),
(18, 8, 0, 'User', 91),
(19, 8, 0, 'User', 92),
(20, 8, 0, 'User', 93),
(21, 8, 0, 'User', 94),
(22, 8, 20, 'User', 95),
(23, 8, 7, 'User', 96),
(24, 8, 14, 'User', 97),
(25, 8, 19, 'User', 98),
(26, 8, 35, 'User', 99),
(27, 8, 26, 'User', 100),
(28, 8, 1, 'User', 101),
(29, 8, 1, 'User', 102),
(30, 8, 1, 'User', 103),
(31, 8, 1, 'User', 104),
(32, 8, 1, 'User', 105),
(33, 8, 1, 'User', 106),
(34, 8, 1, 'User', 107),
(35, 8, 1, 'User', 108),
(36, 8, 1, 'User', 109),
(37, 8, 1, 'User', 110),
(38, 8, 1, 'User', 111),
(39, 8, 1, 'User', 112),
(40, 8, 1, 'User', 113),
(41, 8, 1, 'User', 114),
(42, 8, 1, 'User', 115),
(43, 8, 1, 'User', 116),
(44, 8, 1, 'User', 117),
(45, 8, 1, 'User', 118),
(46, 8, 1, 'User', 119),
(47, 8, 1, 'User', 120),
(48, 8, 1, 'User', 121),
(49, 8, 1, 'User', 122),
(50, 8, 1, 'User', 123),
(51, 8, 1, 'User', 124),
(52, 8, 1, 'User', 125),
(53, 8, 1, 'User', 126),
(54, 8, 1, 'User', 127),
(55, 8, 1, 'User', 128),
(56, 8, 1, 'User', 129),
(57, 8, 1, 'User', 130),
(58, 8, 1, 'User', 131),
(59, 8, 1, 'User', 132),
(60, 8, 1, 'User', 133),
(61, 8, 1, 'User', 134),
(62, 8, 1, 'User', 135),
(63, 8, 1, 'User', 136),
(64, 8, 1, 'User', 137),
(65, 8, 1, 'User', 138),
(66, 8, 1, 'User', 139),
(67, 8, 1, 'User', 140),
(68, 8, 1, 'User', 141),
(69, 8, 1, 'User', 142),
(70, 8, 1, 'User', 143),
(71, 8, 1, 'User', 144),
(72, 8, 1, 'User', 145),
(73, 8, 1, 'User', 146),
(74, 8, 1, 'User', 147),
(75, 8, 1, 'User', 148),
(76, 8, 1, 'User', 149),
(77, 8, 1, 'User', 150),
(78, 8, 1, 'User', 151),
(79, 8, 1, 'User', 152),
(80, 8, 1, 'User', 153),
(81, 8, 1, 'User', 154),
(82, 8, 1, 'User', 155),
(83, 8, 1, 'User', 156),
(84, 8, 1, 'User', 157),
(85, 8, 1, 'User', 158),
(86, 8, 1, 'User', 159),
(87, 8, 1, 'User', 160),
(88, 8, 1, 'User', 161),
(89, 8, 1, 'User', 162),
(90, 8, 1, 'User', 163),
(91, 8, 1, 'User', 164),
(92, 8, 1, 'User', 165),
(93, 8, 1, 'User', 166),
(94, 8, 1, 'User', 167),
(95, 8, 1, 'User', 168),
(96, 8, 1, 'User', 169),
(97, 8, 1, 'User', 170),
(98, 8, 1, 'User', 171),
(99, 8, 1, 'User', 172),
(100, 8, 1, 'User', 174),
(101, 8, 1, 'User', 175),
(102, 8, 1, 'User', 176),
(103, 8, 1, 'User', 177),
(104, 8, 1, 'User', 178),
(105, 8, 1, 'User', 179),
(106, 8, 1, 'User', 180),
(107, 8, 1, 'User', 181),
(108, 8, 1, 'User', 182),
(109, 8, 1, 'User', 183),
(110, 8, 1, 'User', 184),
(111, 8, 1, 'User', 185),
(112, 8, 1, 'User', 186),
(113, 8, 1, 'User', 187),
(114, 8, 1, 'User', 188),
(115, 8, 1, 'User', 189),
(116, 8, 1, 'User', 190),
(117, 8, 1, 'User', 191),
(118, 8, 1, 'User', 192),
(119, 8, 0, 'User', 193),
(120, 8, 1, 'User', 194),
(121, 8, 1, 'User', 195),
(122, 8, 1, 'User', 196),
(123, 8, 1, 'User', 197),
(124, 8, 1, 'User', 198),
(125, 8, 1, 'User', 199),
(126, 8, 1, 'User', 200),
(127, 8, 1, 'User', 201),
(128, 8, 1, 'User', 202),
(129, 8, 1, 'User', 203),
(130, 8, 1, 'User', 204),
(131, 8, 1, 'User', 205),
(132, 8, 1, 'User', 206),
(133, 8, 1, 'User', 207),
(134, 8, 1, 'User', 208),
(135, 8, 1, 'User', 209),
(136, 8, 1, 'User', 210),
(137, 8, 1, 'User', 211),
(138, 8, 1, 'User', 212),
(139, 8, 1, 'User', 213),
(140, 8, 1, 'User', 214),
(141, 8, 1, 'User', 215),
(142, 8, 1, 'User', 216),
(143, 8, 1, 'User', 217),
(144, 8, 1, 'User', 218),
(145, 8, 1, 'User', 219),
(146, 8, 1, 'User', 220),
(147, 8, 1, 'User', 221),
(148, 8, 1, 'User', 222),
(149, 8, 1, 'User', 223),
(150, 8, 1, 'User', 224),
(151, 8, 1, 'User', 225),
(152, 8, 1, 'User', 226),
(153, 8, 1, 'User', 227),
(154, 8, 1, 'User', 228),
(155, 8, 1, 'User', 229),
(156, 8, 1, 'User', 230),
(157, 8, 1, 'User', 232),
(158, 8, 1, 'User', 233),
(159, 8, 1, 'User', 234),
(160, 8, 1, 'User', 235),
(161, 8, 1, 'User', 236),
(162, 8, 1, 'User', 237),
(163, 8, 1, 'User', 238),
(164, 8, 1, 'User', 241),
(165, 8, 1, 'User', 242),
(166, 8, 1, 'User', 243),
(167, 8, 1, 'User', 244),
(168, 8, 1, 'User', 245),
(169, 8, 0, 'User', 246),
(170, 8, 1, 'User', 247),
(171, 8, 1, 'User', 248),
(172, 8, 1, 'User', 249),
(173, 8, 1, 'User', 250),
(174, 8, 1, 'User', 251),
(175, 8, 1, 'User', 252),
(176, 8, 1, 'User', 253),
(177, 8, 1, 'User', 254),
(178, 8, 1, 'User', 255),
(179, 8, 1, 'User', 256),
(180, 8, 1, 'User', 257),
(181, 8, 1, 'User', 258),
(182, 8, 1, 'User', 259),
(183, 8, 1, 'User', 260),
(184, 8, 1, 'User', 261),
(185, 8, 1, 'User', 262),
(186, 8, 1, 'User', 263),
(187, 8, 1, 'User', 264),
(188, 8, 1, 'User', 265),
(189, 8, 0, 'User', 266),
(190, 8, 1, 'User', 267),
(191, 8, 1, 'User', 268),
(192, 8, 1, 'User', 269),
(193, 8, 1, 'User', 270),
(194, 8, 1, 'User', 271),
(195, 8, 1, 'User', 272),
(196, 8, 0, 'User', 273),
(197, 8, 1, 'User', 274),
(198, 8, 1, 'User', 275),
(199, 8, 1, 'User', 276),
(200, 8, 1, 'User', 277),
(201, 8, 1, 'User', 278),
(202, 8, 1, 'User', 279),
(203, 8, 1, 'User', 280),
(204, 8, 1, 'User', 281),
(205, 8, 1, 'User', 282),
(206, 8, 1, 'User', 283),
(207, 8, 1, 'User', 284),
(208, 8, 1, 'User', 285),
(209, 8, 1, 'User', 286),
(210, 8, 1, 'User', 287),
(211, 8, 1, 'User', 288),
(212, 8, 1, 'User', 289),
(213, 8, 1, 'User', 290),
(214, 8, 12, 'User', 291),
(215, 8, 1, 'User', 292),
(216, 8, 1, 'User', 293),
(217, 8, 1, 'User', 294),
(218, 8, 1, 'User', 295),
(219, 8, 1, 'User', 296),
(220, 8, 1, 'User', 297),
(221, 8, 1, 'User', 298),
(222, 8, 1, 'User', 299),
(223, 8, 1, 'User', 300),
(224, 8, 1, 'User', 301),
(225, 8, 1, 'User', 302),
(226, 8, 1, 'User', 303),
(227, 8, 1, 'User', 304),
(228, 8, 1, 'User', 305),
(229, 8, 24, 'User', 42),
(230, 8, 17, 'User', 46),
(231, 8, 0, 'User', 83),
(232, 8, 0, 'User', 85),
(233, 8, 0, 'User', 86),
(234, 8, 0, 'User', 87),
(235, 8, 0, 'User', 88),
(236, 8, 0, 'User', 89),
(237, 8, 0, 'User', 90),
(238, 8, 0, 'User', 91),
(239, 8, 0, 'User', 92),
(240, 8, 0, 'User', 93),
(241, 8, 0, 'User', 94),
(242, 8, 20, 'User', 95),
(243, 8, 7, 'User', 96),
(244, 8, 14, 'User', 97),
(245, 8, 19, 'User', 98),
(246, 8, 35, 'User', 99),
(247, 8, 26, 'User', 100),
(248, 8, 1, 'User', 101),
(249, 8, 1, 'User', 102),
(250, 8, 1, 'User', 103),
(251, 8, 1, 'User', 104),
(252, 8, 1, 'User', 105),
(253, 8, 1, 'User', 106),
(254, 8, 1, 'User', 107),
(255, 8, 1, 'User', 108),
(256, 8, 1, 'User', 109),
(257, 8, 1, 'User', 110),
(258, 8, 1, 'User', 111),
(259, 8, 1, 'User', 112),
(260, 8, 1, 'User', 113),
(261, 8, 1, 'User', 114),
(262, 8, 1, 'User', 115),
(263, 8, 1, 'User', 116),
(264, 8, 1, 'User', 117),
(265, 8, 1, 'User', 118),
(266, 8, 1, 'User', 119),
(267, 8, 1, 'User', 120),
(268, 8, 1, 'User', 121),
(269, 8, 1, 'User', 122),
(270, 8, 1, 'User', 123),
(271, 8, 1, 'User', 124),
(272, 8, 1, 'User', 125),
(273, 8, 1, 'User', 126),
(274, 8, 1, 'User', 127),
(275, 8, 1, 'User', 128),
(276, 8, 1, 'User', 129),
(277, 8, 1, 'User', 130),
(278, 8, 1, 'User', 131),
(279, 8, 1, 'User', 132),
(280, 8, 1, 'User', 133),
(281, 8, 1, 'User', 134),
(282, 8, 1, 'User', 135),
(283, 8, 1, 'User', 136),
(284, 8, 1, 'User', 137),
(285, 8, 1, 'User', 138),
(286, 8, 1, 'User', 139),
(287, 8, 1, 'User', 140),
(288, 8, 1, 'User', 141),
(289, 8, 1, 'User', 142),
(290, 8, 1, 'User', 143),
(291, 8, 1, 'User', 144),
(292, 8, 1, 'User', 145),
(293, 8, 1, 'User', 146),
(294, 8, 1, 'User', 147),
(295, 8, 1, 'User', 148),
(296, 8, 1, 'User', 149),
(297, 8, 1, 'User', 150),
(298, 8, 1, 'User', 151),
(299, 8, 1, 'User', 152),
(300, 8, 1, 'User', 153),
(301, 8, 1, 'User', 154),
(302, 8, 1, 'User', 155),
(303, 8, 1, 'User', 156),
(304, 8, 1, 'User', 157),
(305, 8, 1, 'User', 158),
(306, 8, 1, 'User', 159),
(307, 8, 1, 'User', 160),
(308, 8, 1, 'User', 161),
(309, 8, 1, 'User', 162),
(310, 8, 1, 'User', 163),
(311, 8, 1, 'User', 164),
(312, 8, 1, 'User', 165),
(313, 8, 1, 'User', 166),
(314, 8, 1, 'User', 167),
(315, 8, 1, 'User', 168),
(316, 8, 1, 'User', 169),
(317, 8, 1, 'User', 170),
(318, 8, 1, 'User', 171),
(319, 8, 1, 'User', 172),
(320, 8, 1, 'User', 174),
(321, 8, 1, 'User', 175),
(322, 8, 1, 'User', 176),
(323, 8, 1, 'User', 177),
(324, 8, 1, 'User', 178),
(325, 8, 1, 'User', 179),
(326, 8, 1, 'User', 180),
(327, 8, 1, 'User', 181),
(328, 8, 1, 'User', 182),
(329, 8, 1, 'User', 183),
(330, 8, 1, 'User', 184),
(331, 8, 1, 'User', 185),
(332, 8, 1, 'User', 186),
(333, 8, 1, 'User', 187),
(334, 8, 1, 'User', 188),
(335, 8, 1, 'User', 189),
(336, 8, 1, 'User', 190),
(337, 8, 1, 'User', 191),
(338, 8, 1, 'User', 192),
(339, 8, 0, 'User', 193),
(340, 8, 1, 'User', 194),
(341, 8, 1, 'User', 195),
(342, 8, 1, 'User', 196),
(343, 8, 1, 'User', 197),
(344, 8, 1, 'User', 198),
(345, 8, 1, 'User', 199),
(346, 8, 1, 'User', 200),
(347, 8, 1, 'User', 201),
(348, 8, 1, 'User', 202),
(349, 8, 1, 'User', 203),
(350, 8, 1, 'User', 204),
(351, 8, 1, 'User', 205),
(352, 8, 1, 'User', 206),
(353, 8, 1, 'User', 207),
(354, 8, 1, 'User', 208),
(355, 8, 1, 'User', 209),
(356, 8, 1, 'User', 210),
(357, 8, 1, 'User', 211),
(358, 8, 1, 'User', 212),
(359, 8, 1, 'User', 213),
(360, 8, 1, 'User', 214),
(361, 8, 1, 'User', 215),
(362, 8, 1, 'User', 216),
(363, 8, 1, 'User', 217),
(364, 8, 1, 'User', 218),
(365, 8, 1, 'User', 219),
(366, 8, 1, 'User', 220),
(367, 8, 1, 'User', 221),
(368, 8, 1, 'User', 222),
(369, 8, 1, 'User', 223),
(370, 8, 1, 'User', 224),
(371, 8, 1, 'User', 225),
(372, 8, 1, 'User', 226),
(373, 8, 1, 'User', 227),
(374, 8, 1, 'User', 228),
(375, 8, 1, 'User', 229),
(376, 8, 1, 'User', 230),
(377, 8, 1, 'User', 232),
(378, 8, 1, 'User', 233),
(379, 8, 1, 'User', 234),
(380, 8, 1, 'User', 235),
(381, 8, 1, 'User', 236),
(382, 8, 1, 'User', 237),
(383, 8, 1, 'User', 238),
(384, 8, 1, 'User', 241),
(385, 8, 1, 'User', 242),
(386, 8, 1, 'User', 243),
(387, 8, 1, 'User', 244),
(388, 8, 1, 'User', 245),
(389, 8, 0, 'User', 246),
(390, 8, 1, 'User', 247),
(391, 8, 1, 'User', 248),
(392, 8, 1, 'User', 249),
(393, 8, 1, 'User', 250),
(394, 8, 1, 'User', 251),
(395, 8, 1, 'User', 252),
(396, 8, 1, 'User', 253),
(397, 8, 1, 'User', 254),
(398, 8, 1, 'User', 255),
(399, 8, 1, 'User', 256),
(400, 8, 1, 'User', 257),
(401, 8, 1, 'User', 258),
(402, 8, 1, 'User', 259),
(403, 8, 1, 'User', 260),
(404, 8, 1, 'User', 261),
(405, 8, 1, 'User', 262),
(406, 8, 1, 'User', 263),
(407, 8, 1, 'User', 264),
(408, 8, 1, 'User', 265),
(409, 8, 0, 'User', 266),
(410, 8, 1, 'User', 267),
(411, 8, 1, 'User', 268),
(412, 8, 1, 'User', 269),
(413, 8, 1, 'User', 270),
(414, 8, 1, 'User', 271),
(415, 8, 1, 'User', 272),
(416, 8, 0, 'User', 273),
(417, 8, 1, 'User', 274),
(418, 8, 1, 'User', 275),
(419, 8, 1, 'User', 276),
(420, 8, 1, 'User', 277),
(421, 8, 1, 'User', 278),
(422, 8, 1, 'User', 279),
(423, 8, 1, 'User', 280),
(424, 8, 1, 'User', 281),
(425, 8, 1, 'User', 282),
(426, 8, 1, 'User', 283),
(427, 8, 1, 'User', 284),
(428, 8, 1, 'User', 285),
(429, 8, 1, 'User', 286),
(430, 8, 1, 'User', 287),
(431, 8, 1, 'User', 288),
(432, 8, 1, 'User', 289),
(433, 8, 1, 'User', 290),
(434, 8, 12, 'User', 291),
(435, 8, 1, 'User', 292),
(436, 8, 1, 'User', 293),
(437, 8, 1, 'User', 294),
(438, 8, 1, 'User', 295),
(439, 8, 1, 'User', 296),
(440, 8, 1, 'User', 297),
(441, 8, 1, 'User', 298),
(442, 8, 1, 'User', 299),
(443, 8, 1, 'User', 300),
(444, 8, 1, 'User', 301),
(445, 8, 1, 'User', 302),
(446, 8, 1, 'User', 303),
(447, 8, 1, 'User', 304),
(448, 8, 1, 'User', 305),
(449, 8, 1, 'User', 306),
(450, 8, 1, 'User', 307),
(451, 8, 1, 'User', 308),
(452, 8, 1, 'User', 309),
(453, 8, 1, 'User', 310),
(454, 8, 1, 'User', 311),
(455, 8, 1, 'User', 312),
(456, 8, 1, 'User', 313),
(457, 8, 1, 'User', 314),
(458, 8, 1, 'User', 315),
(459, 8, 1, 'User', 316),
(460, 8, 1, 'User', 317),
(461, 8, 1, 'User', 318),
(462, 8, 1, 'User', 319),
(463, 8, 1, 'User', 320),
(464, 8, 1, 'User', 321),
(465, 8, 1, 'User', 322),
(466, 8, 1, 'User', 323),
(467, 8, 1, 'User', 324),
(468, 8, 1, 'User', 325),
(469, 8, 1, 'User', 326),
(470, 8, 1, 'User', 327),
(471, 8, 1, 'User', 328),
(472, 8, 1, 'User', 329),
(473, 8, 1, 'User', 330),
(474, 8, 1, 'User', 331),
(475, 8, 1, 'User', 332),
(476, 8, 1, 'User', 333),
(477, 8, 1, 'User', 334),
(478, 8, 1, 'User', 335),
(479, 8, 1, 'User', 336);

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
(1, 5, 'Structure_Item', 2, 3),
(2, 5, 'Structure', 1, 4),
(5, 7, 'User', 46, 1),
(6, 7, 'User', 83, 0),
(7, 7, 'User', 85, 0),
(8, 7, 'User', 86, 0),
(9, 7, 'User', 87, 0),
(10, 7, 'User', 88, 0),
(11, 7, 'User', 89, 0),
(12, 7, 'User', 90, 0),
(13, 7, 'User', 91, 0),
(14, 7, 'User', 92, 0),
(15, 7, 'User', 93, 0),
(16, 7, 'User', 94, 0),
(17, 7, 'User', 95, 0),
(18, 7, 'User', 96, 0),
(19, 7, 'User', 97, 0),
(20, 7, 'User', 98, 0),
(21, 7, 'User', 99, 0),
(22, 7, 'User', 100, 0),
(23, 7, 'User', 101, 0),
(24, 7, 'User', 102, 0),
(25, 7, 'User', 103, 0),
(26, 7, 'User', 104, 0),
(27, 7, 'User', 105, 0),
(28, 7, 'User', 106, 0),
(29, 7, 'User', 107, 0),
(30, 7, 'User', 108, 0),
(31, 7, 'User', 109, 0),
(32, 7, 'User', 110, 0),
(33, 7, 'User', 111, 0),
(34, 7, 'User', 112, 0),
(35, 7, 'User', 113, 0),
(36, 7, 'User', 114, 0),
(37, 7, 'User', 115, 0),
(38, 7, 'User', 116, 0),
(39, 7, 'User', 117, 0),
(40, 7, 'User', 118, 0),
(41, 7, 'User', 119, 0),
(42, 7, 'User', 120, 0),
(43, 7, 'User', 121, 0),
(44, 7, 'User', 122, 0),
(45, 7, 'User', 123, 0),
(46, 7, 'User', 124, 0),
(47, 7, 'User', 125, 0),
(48, 7, 'User', 126, 0),
(49, 7, 'User', 127, 0),
(50, 7, 'User', 128, 0),
(51, 7, 'User', 129, 0),
(52, 7, 'User', 130, 0),
(53, 7, 'User', 131, 0),
(54, 7, 'User', 132, 0),
(55, 7, 'User', 133, 0),
(56, 7, 'User', 134, 0),
(57, 7, 'User', 135, 0),
(58, 7, 'User', 136, 0),
(59, 7, 'User', 137, 0),
(60, 7, 'User', 138, 0),
(61, 7, 'User', 139, 0),
(62, 7, 'User', 140, 0),
(63, 7, 'User', 141, 0),
(64, 7, 'User', 142, 0),
(65, 7, 'User', 143, 0),
(66, 7, 'User', 144, 0),
(67, 7, 'User', 145, 0),
(68, 7, 'User', 146, 0),
(69, 7, 'User', 147, 0),
(70, 7, 'User', 148, 0),
(71, 7, 'User', 149, 0),
(72, 7, 'User', 150, 0),
(73, 7, 'User', 151, 0),
(74, 7, 'User', 152, 0),
(75, 7, 'User', 153, 0),
(76, 7, 'User', 154, 0),
(77, 7, 'User', 155, 0),
(78, 7, 'User', 156, 0),
(79, 7, 'User', 157, 0),
(80, 7, 'User', 158, 0),
(81, 7, 'User', 159, 0),
(82, 7, 'User', 160, 0),
(83, 7, 'User', 161, 0),
(84, 7, 'User', 162, 0),
(85, 7, 'User', 163, 0),
(86, 7, 'User', 164, 0),
(87, 7, 'User', 165, 0),
(88, 7, 'User', 166, 0),
(89, 7, 'User', 167, 0),
(90, 7, 'User', 168, 0),
(91, 7, 'User', 169, 0),
(92, 7, 'User', 170, 0),
(93, 7, 'User', 171, 0),
(94, 7, 'User', 172, 0),
(95, 7, 'User', 174, 0),
(96, 7, 'User', 175, 0),
(97, 7, 'User', 176, 0),
(98, 7, 'User', 177, 0),
(99, 7, 'User', 178, 0),
(100, 7, 'User', 179, 0),
(101, 7, 'User', 180, 0),
(102, 7, 'User', 181, 0),
(103, 7, 'User', 182, 0),
(104, 7, 'User', 183, 0),
(105, 7, 'User', 184, 0),
(106, 7, 'User', 185, 0),
(107, 7, 'User', 186, 0),
(108, 7, 'User', 187, 0),
(109, 7, 'User', 188, 0),
(110, 7, 'User', 189, 0),
(111, 7, 'User', 190, 0),
(112, 7, 'User', 191, 0),
(113, 7, 'User', 192, 0),
(114, 7, 'User', 193, 0),
(115, 7, 'User', 194, 0),
(116, 7, 'User', 195, 0),
(117, 7, 'User', 196, 0),
(118, 7, 'User', 197, 0),
(119, 7, 'User', 198, 0),
(120, 7, 'User', 199, 0),
(121, 7, 'User', 200, 0),
(122, 7, 'User', 201, 0),
(123, 7, 'User', 202, 0),
(124, 7, 'User', 203, 0),
(125, 7, 'User', 204, 0),
(126, 7, 'User', 205, 0),
(127, 7, 'User', 206, 0),
(128, 7, 'User', 207, 0),
(129, 7, 'User', 208, 0),
(130, 7, 'User', 209, 0),
(131, 7, 'User', 210, 0),
(132, 7, 'User', 211, 0),
(133, 7, 'User', 212, 0),
(134, 7, 'User', 213, 0),
(135, 7, 'User', 214, 0),
(136, 7, 'User', 215, 0),
(137, 7, 'User', 216, 0),
(138, 7, 'User', 217, 0),
(139, 7, 'User', 218, 0),
(140, 7, 'User', 219, 0),
(141, 7, 'User', 220, 0),
(142, 7, 'User', 221, 0),
(143, 7, 'User', 222, 0),
(144, 7, 'User', 223, 0),
(145, 7, 'User', 224, 0),
(146, 7, 'User', 225, 0),
(147, 7, 'User', 226, 0),
(148, 7, 'User', 227, 0),
(149, 7, 'User', 228, 0),
(150, 7, 'User', 229, 0),
(151, 7, 'User', 230, 0),
(152, 7, 'User', 232, 0),
(153, 7, 'User', 233, 0),
(154, 7, 'User', 234, 0),
(155, 7, 'User', 235, 0),
(156, 7, 'User', 236, 0),
(157, 7, 'User', 237, 0),
(158, 7, 'User', 238, 0),
(159, 7, 'User', 241, 0),
(160, 7, 'User', 242, 0),
(161, 7, 'User', 243, 0),
(162, 7, 'User', 244, 0),
(163, 7, 'User', 245, 0),
(164, 7, 'User', 246, 0),
(165, 7, 'User', 247, 0),
(166, 7, 'User', 248, 0),
(167, 7, 'User', 249, 0),
(168, 7, 'User', 250, 0),
(169, 7, 'User', 251, 0),
(170, 7, 'User', 252, 0),
(171, 7, 'User', 253, 0),
(172, 7, 'User', 254, 0),
(173, 7, 'User', 255, 0),
(174, 7, 'User', 256, 0),
(175, 7, 'User', 257, 0),
(176, 7, 'User', 258, 0),
(177, 7, 'User', 259, 0),
(178, 7, 'User', 260, 0),
(179, 7, 'User', 261, 0),
(180, 7, 'User', 262, 0),
(181, 7, 'User', 263, 0),
(182, 7, 'User', 264, 0),
(183, 7, 'User', 265, 0),
(184, 7, 'User', 266, 0),
(185, 7, 'User', 267, 0),
(186, 7, 'User', 268, 0),
(187, 7, 'User', 269, 0),
(188, 7, 'User', 270, 0),
(189, 7, 'User', 271, 0),
(190, 7, 'User', 272, 0),
(191, 7, 'User', 273, 0),
(192, 7, 'User', 274, 0),
(193, 7, 'User', 275, 0),
(194, 7, 'User', 276, 0),
(195, 7, 'User', 277, 0),
(196, 7, 'User', 278, 0),
(197, 7, 'User', 279, 0),
(198, 7, 'User', 280, 0),
(199, 7, 'User', 281, 0),
(200, 7, 'User', 282, 0),
(201, 7, 'User', 283, 0),
(202, 7, 'User', 284, 0),
(203, 7, 'User', 285, 0),
(204, 7, 'User', 286, 0),
(205, 7, 'User', 287, 0),
(206, 7, 'User', 288, 0),
(207, 7, 'User', 289, 0),
(208, 7, 'User', 290, 0),
(209, 7, 'User', 291, 0),
(210, 7, 'User', 292, 0),
(211, 7, 'User', 293, 0),
(212, 7, 'User', 294, 0),
(213, 7, 'User', 295, 0),
(214, 7, 'User', 296, 0),
(215, 7, 'User', 297, 0),
(216, 7, 'User', 298, 0),
(217, 7, 'User', 299, 0),
(218, 7, 'User', 300, 0),
(219, 7, 'User', 301, 0),
(220, 7, 'User', 302, 0),
(221, 7, 'User', 303, 0),
(222, 7, 'User', 304, 0),
(223, 7, 'User', 305, 0),
(224, 7, 'User', 306, 0),
(225, 7, 'User', 307, 0),
(226, 7, 'User', 308, 0),
(227, 7, 'User', 309, 0),
(228, 7, 'User', 310, 0),
(229, 7, 'User', 311, 0),
(230, 7, 'User', 312, 0),
(231, 7, 'User', 313, 0),
(232, 7, 'User', 314, 0),
(233, 7, 'User', 315, 0),
(234, 7, 'User', 316, 0),
(235, 7, 'User', 317, 0),
(236, 7, 'User', 318, 0),
(237, 7, 'User', 319, 0),
(238, 7, 'User', 320, 0),
(239, 7, 'User', 321, 0),
(240, 7, 'User', 322, 0),
(241, 7, 'User', 323, 0),
(242, 7, 'User', 324, 0),
(243, 7, 'User', 325, 0),
(244, 7, 'User', 326, 0),
(245, 7, 'User', 327, 0),
(246, 7, 'User', 328, 0),
(247, 7, 'User', 329, 0),
(248, 7, 'User', 330, 0),
(249, 7, 'User', 331, 0),
(250, 7, 'User', 332, 0),
(251, 7, 'User', 333, 0),
(252, 7, 'User', 334, 0),
(253, 7, 'User', 335, 0),
(254, 7, 'User', 336, 0);

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
(4, 5, 'Значение 4'),
(6, 1, '500'),
(7, 1, '400'),
(8, 1, '300'),
(9, 1, '200'),
(10, 1, '250'),
(11, 1, '150'),
(12, 1, '100'),
(13, 1, '0'),
(14, 10, 'Индивидуальное занятие'),
(15, 10, 'Групповое занятие');

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
(49, 9, 'https://vk.com/just.vika', 'User', 334);

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
(1, 4, 'Описание товара1', 'Structure_Item', 2),
(4, 3, 'Какое-то значение дополнительного свойства \"Параметры\"', 'Structure', 1),
(6, 3, 'параметры1', 'Structure_Item', 2),
(7, 4, 'Описание товара2', 'Structure_Item', 2);

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
  `meta_title` varchar(100) DEFAULT NULL,
  `meta_description` text,
  `meta_keywords` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `Structure`
--

INSERT INTO `Structure` (`id`, `title`, `parent_id`, `path`, `action`, `template_id`, `description`, `properties_list`, `active`, `sorting`, `meta_title`, `meta_description`, `meta_keywords`) VALUES
(1, 'Магазин', 0, 'shop', 'catalog', 1, 'Интернет-магазин', 'a:1:{i:0;i:5;}', 1, 0, '', '', ''),
(2, 'Спорт', 1, 'sport', 'catalog', 1, '', '', 1, 0, '', '', ''),
(3, 'Теннис', 2, 'tennis', 'catalog', 1, '', '', 1, 0, '', '', ''),
(4, 'Футбол', 2, 'football', 'catalog', 1, '', '', 1, 0, '', '', ''),
(5, 'Индексная страница', 0, '', 'index', 5, '', 'b:0;', 1, 0, '', '', ''),
(6, 'Административный раздел', 0, 'admin', 'admin/index', 3, '', 'b:0;', 1, 0, '', '', ''),
(7, 'Личный кабинет', 0, 'user', 'user', 4, '', 'b:0;', 1, 0, '', '', ''),
(8, 'Иерархия классов', 9, 'models', 'documentation/models', 1, 'Общая структура системы. Описание стандартных классов, их свойств и методов.', '', 1, 0, '', '', ''),
(9, 'Документация', 0, 'documentation', 'documentation/index', 1, 'Руководство по использованию системы', '', 1, 0, '', '', ''),
(13, 'Панель управления', 0, 'musadm', 'musadm/index', 4, '', 'b:0;', 1, 0, '', '', ''),
(14, 'Авторизация', 13, 'authorize', 'musadm/authorize', 1, '', 'b:0;', 1, 0, '', '', ''),
(15, 'Спорт2', 1, '', '', 0, '', 'b:0;', 1, 0, NULL, NULL, NULL),
(16, 'Спорт3', 1, '', '', 0, '', 'b:0;', 1, 0, NULL, NULL, NULL),
(17, 'Спорт4', 1, '', '', 0, '', 'b:0;', 1, 0, NULL, NULL, NULL),
(18, 'Спорт5', 1, '', '', 0, '', 'b:0;', 0, 0, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `Structure_Item`
--

CREATE TABLE `Structure_Item` (
  `id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `description` text,
  `properties_list` text NOT NULL,
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

INSERT INTO `Structure_Item` (`id`, `title`, `parent_id`, `description`, `properties_list`, `active`, `meta_title`, `meta_description`, `meta_keywords`, `path`, `sorting`) VALUES
(1, 'Футбольный мячь', 4, '', '', 1, 'SEO title', 'SEO описание', 'SEO ключевые слова', '1', 0),
(2, 'Кросовки Nike', 4, '', 'a:3:{i:0;i:5;i:1;i:3;i:2;i:4;}', 1, '', '', '', '2', 0),
(3, 'Название', 1, NULL, 'b:0;', 1, NULL, NULL, NULL, '', 0),
(4, 'Название2', 1, NULL, 'b:0;', 1, NULL, NULL, NULL, '', 0);

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
  `superuser` int(11) NOT NULL DEFAULT '0',
  `properties_list` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `User`
--

INSERT INTO `User` (`id`, `name`, `surname`, `patronimyc`, `phone_number`, `email`, `login`, `password`, `group_id`, `register_date`, `active`, `superuser`, `properties_list`) VALUES
(1, 'Егор', 'Козырев', 'Алексеевич', '8-980-378-28-56', 'creative27016@gmail.com', 'alexoufx', '4a7d1ed414474e4033ac29ccb8653d9b', 1, '0000-00-00', 1, 1, ''),
(2, 'Имя', 'Фамилия', '', '8-980-888-88-88', 'test@email.ru', 'test', '098f6bcd4621d373cade4e832627b4f6', 2, '2018-02-15', 1, 0, 'b:0;'),
(42, 'Александр', 'Булгаков', '', '+79087801122', '', 'БА', 'd93591bdf7860e1e4ee2fca799911215', 4, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(46, 'Оксана', 'Полтева', '', '+79205764079', '', 'ПО', 'd93591bdf7860e1e4ee2fca799911215', 4, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(83, 'Алина', 'Романович', '', '+79192882062', '', 'РА', 'd93591bdf7860e1e4ee2fca799911215', 4, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(85, 'Артур', 'Герус', '', '30-18-77', '', 'd', '8277e0910d750195b448797616e091ad', 3, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(86, 'Дарья', 'Черных', '', '+79155665673', '', 'ЧД', 'd93591bdf7860e1e4ee2fca799911215', 4, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(87, 'Руслан', 'Галяутдинов', '', '+79202090014', '', 'ГР', 'd93591bdf7860e1e4ee2fca799911215', 4, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(88, 'Карина', 'Белякова', '', '+79155601114', '', 'БК', 'd93591bdf7860e1e4ee2fca799911215', 4, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(89, 'Владимир', 'Варжавинов', '', '+79606336651', '', 'ВВ', 'd93591bdf7860e1e4ee2fca799911215', 4, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(90, 'Вадим', 'Фёдоров', '', '+79092019737', '', 'ФВ', 'd93591bdf7860e1e4ee2fca799911215', 4, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(91, 'Андрей', 'Матвиенко', '', '+79045311244', '', 'МА', 'd93591bdf7860e1e4ee2fca799911215', 4, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(92, 'Андрей', 'Жуйков', '', '+79040872458', '', 'ЖА', 'd93591bdf7860e1e4ee2fca799911215', 4, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(93, 'Айарпи', 'Джилавян', '', '+79606371162', '', 'ДА', 'd93591bdf7860e1e4ee2fca799911215', 4, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(94, 'Иван', 'Рымарев', '', '+79040904607', '', 'РИ', 'd93591bdf7860e1e4ee2fca799911215', 4, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(95, 'Максим', 'Рябых ', '', '+79611640794', '', 'РяМа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(96, 'Евгений', 'Байдиков', '', '9511340707', '', 'БаЕв', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(97, 'Дмитрий', 'Тетюхин', '', '9107419097, мама9103295637', '', 'ТеДи', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(98, 'Оксана', 'Захарова', '', '9511406093', '', 'ЗаОк', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(99, 'Чавес', 'Дебора', '', '9205967925', '', 'ДеЧа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(100, 'Михаил', 'Диченко', '', '9040822225', '', 'ДиМи', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(101, 'Дарья', 'Ведерникова', '', '9192884237', '', 'ВеДа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(102, 'Юлия', 'Савенкова', '', '9606323377', '', 'СаЮл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(103, 'Юлия', 'Дворецкая', '', '9155636799', '', 'ДвЮл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(104, 'Михаил', 'Кравцов', '', '9102237815, мама 9155274452', '', 'КрМи', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(105, 'Ангелина', 'Тараник', '', '9803753021 папа', '', 'ТаАн', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(106, 'Мария', 'Котова', '', '9192213076', '', 'КоПо', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(107, 'Аврора', 'Мочалова', '', '9202002087', '', 'МоАв', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(108, 'Карина', 'Чумак', '', '9092020721', '', 'ЧуКа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(109, 'Дмитрий', 'Сидлецкий', '', '9205792055', '', 'СиДм', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(110, 'Денис', 'Егоров', '', '9155730675', '', 'ЕгДе', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(111, 'Варвара', 'Худасова', '', '9087814060', '', 'ХуВа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(112, 'Ирина', 'Васильченко', '', '9092055444', '', 'ВаИр', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(113, 'Даниил', 'Немыкин', '', '9065654967', '', 'НеДа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(114, 'Андрей', 'Кузьмичев', '', '9040908554, мама9511592054', '', 'КуАн', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(115, 'Ксения', 'Игнатова', '', '9040941155', '', 'ИгКс', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(116, 'Мария', 'Косухина', '', '9202065482, 9192850039', '', 'КоМа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(117, 'Марина', 'Орлова', '', '9066005833', '', 'ОрМа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(118, 'Алиса', 'Дорохова', '', '9155750794', '', 'ДоАл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(119, 'Константин', 'Баландин', '', '9524349900', '', 'БаКо', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(120, 'Анна', 'Корнеева', '', '9805291851', '', 'КоАн', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(121, 'Светлана', 'Лукашова', '', '9511469962', '', 'ЛуСв', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(122, 'София', 'Корнеева ', '', '9805291851', '', 'КоСо', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(123, 'Диляра', 'Новак', '', '9606404124', '', 'НоДи', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(124, 'Лия', 'Склярова', '', '9092094948', '', 'СкЛи', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(125, 'Елизавета', 'Потатушкина', '', '9155764370', '', 'ПоЕл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(126, 'Евгения', 'Цветкова', '', '9611777964', '', 'ЦвЕв', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(127, 'Алина', 'Глебова', '', '9805253750', '', 'ГлАн', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(128, 'Алексей', 'Горошко', '', '9192822866', '', 'ГоАл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(129, 'Виолетта', 'Корсакова', '', 'мама9192250899, 9194365532', '', 'КоВи', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(130, 'Екатерина', 'Лозовская', '', '9606753635', '', 'ЛоЕк', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(131, 'Наталья', 'Павлик', '', '9066048283', '', 'ПаНа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(132, 'Анастасия', 'Арчибасова', '', '9606262562', '', 'АрАн', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(133, 'Виктория', 'Думанова', '', '9205665205', '', 'ДуВи', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(134, 'Ольга', 'Малькова', '', '9102258020', '', 'МаОл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(135, 'Алёна', 'Баймакова', '', '9092043065', '', 'БаАл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(136, 'Алиса', 'Лылова', '', '', '', 'ЛыАл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(137, 'Константин', 'Клёсов', '', '9040816850', '', 'КлКо', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(138, 'Анна', 'Лылова', '', '', '', 'ЛыАн', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(139, 'Владимир', 'Кузнецов', '', '9205750117', '', 'КуВл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(140, 'Владислав', 'Егоров ', '', '9155730675', '', 'ЕгВл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(141, 'Анастасия', 'Жарикова', '', '9087820943', '', 'ЖаАн', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(142, 'София', 'Курбатова', '', '9087870319', '', 'КуСо', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(143, 'Анастасия', 'Пыркина', '', '9056737739', '', 'ПыАн', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(144, 'Оксана', 'Зиневич', '', '9194374668', '', 'ЗиОк', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(145, 'Екатерина', 'Кобзева', '', '9524331330', '', 'КоЕк', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(146, 'Кирилл', 'Катаржнов', '', '9517624136', '', 'КаКи', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(147, 'Жаломба', 'Селештино', '', '9155297143', '', 'СеЖа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(148, 'Ася', 'Дугнист', '', '9087867792', '', 'ДуАс', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(149, 'Кирилл', 'Пивнев', '', '9524399874', '', 'ПиКи', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(150, 'Ангелина', 'Клочева', '', '9205815530', '', 'КлАн', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(151, 'Кристина', 'Гудкова', '', '9995190691', '', 'ГуКр', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(152, 'Елена', 'Сухобрус', '', '9036424424', '', 'СуЕл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(153, 'Мурадлы', 'Фидан', '', 'мама9524205114, 9803261145', '', 'ФиМу', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(154, 'Марина', 'Ерофеева', '', '9205896526', '', 'ЕрМа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(155, 'Наталья', 'Васильева', '', '9192877530', '', 'ВаНа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(156, 'Юлианна', 'Пронина', '', '9202653722', '', 'ПрЮл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(157, 'Наталья', 'Масс', '', '9606267288', '', 'МаНа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(158, 'Светлана', 'Исаева', '', '9102264633', '', 'ИсСв', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(159, 'Анатолий', 'Самокиша', '', '9205768223, папа9623082588', '', 'СаАн', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(160, 'Светлана', 'Саак', '', '9202001717', '', 'СаСв', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(161, 'Ирина', 'Шилова', '', '9102288507', '', 'ШиИр', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(162, 'Алина', 'Малькова', '', '9040862858', '', 'МаАл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(163, 'Оксана', 'Гринько', '', '9103263030', '', 'ГрОк', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(164, 'Владислав', 'Цунаев', '', '9803255558', '', 'ЦуВл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(165, 'Екатерина', 'Швецова', '', '9087811417', '', 'ШвЕк', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(166, 'Яна', 'Акиньшина', '', '89511357177', '', 'АкЯн', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(167, 'Юлия', 'Фролова', '', 'мама9611737216, 9606305470', '', 'ФрЮл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(168, 'Алина', 'Гунько', '', '9103611195', '', 'ГуАл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(169, 'Ольга', 'Славная', '', '9040877121', '', 'СлОл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(170, 'Юлия', 'Довбыш', '', '9066004550', '', 'ДоЮл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(171, 'Анна', 'Ермолина', '', '9092035014', '', 'ЕрАн', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(172, 'Сергей', 'Миронов', '', '9155257009 9087897257, мама 9803242721', '', 'МиСе', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(174, 'Маргарита', 'Кононова', '', '9194375899', '', 'КонМа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(175, 'Ксения', 'Миженина', '', '9107372122', '', 'МиКс', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(176, 'Дарья', 'Еремина', '', '9087811270', '', 'ЕрДа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(177, 'Анна', 'Арчибасова', '', '', '', 'АрчАн', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(178, 'Сергей', 'Картамышев', '', '9045334421', '', 'КаСе', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(179, 'Екатерина', 'Лаврова', '', 'анкет9045307762, осн9040883998 ', '', 'ЛаЕк', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(180, 'Алексей', 'Евтушенко', '', '9087826599', '', 'ЕвАл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(181, 'Вадим', 'Баранов', '', '9155271114', '', 'БаВа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(182, 'Дмитрий', 'Тагиев', '', 'мама9045369383, 9080806456', '', 'ТаДм', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(183, 'Юлия', 'Дель', '', '9805269096', '', 'ДеЮл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(184, 'Владислав', 'Краснов', '', '9202012313', '', 'КрВл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(185, 'Ирина', 'Пуляева', '', '9205825195, 9384065884', '', 'ПуИр', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(186, 'Владислав', 'Шейченко', '', '9611704948, мама9036421971', '', 'ШеВл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(187, 'Анастасия', 'Зубарева', '', '', '', 'ЗуАн', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(188, 'Радмила', 'Мартынова', '', '9803772116, 9107414966', '', 'МаРа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(189, 'Ольга', 'Сырвачева', '', '9087890517', '', 'СыОл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(190, 'Вадим', 'Ляпин', '', '9155688019мама', '', 'ЛяВа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(191, 'Дарья', 'Сидорова', '', '9511326444', '', 'СиДа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(192, 'Екатерина', 'Раушенбах', '', '', '', 'РаЕк', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(193, 'Павел', 'Сайганов', '', '+79290028122, +79040859023', '', 'СП', 'd93591bdf7860e1e4ee2fca799911215', 4, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(194, 'Марина', 'Резникова', '', '9304000564', '', 'РеМа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(195, 'Егор', 'Попов', '', '9194307480, 9155783090', '', 'ПоЕг', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(196, 'Римма', 'Ремизова', '', '9202068712', '', 'РеРи', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(197, 'Маргарита', 'Ремизова', '', '9202044725', '', 'РемМа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(198, 'Игорь', 'Самойлов', '', '9038845902', '', 'СаИг', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(199, 'Елена', 'Сидоренко', '', '9192870280', '', 'СиЕл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(200, 'Анастасия', 'Сидорова', '', '9155750240', '', 'СиАн', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(201, 'Дмитрий', 'Сорокин', '', '9611728486', '', 'СоДм', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(202, 'Ольга', 'Тодощук', '', '9205545813', '', 'ТоОл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(203, 'Егор', 'Тулинов', '', 'мама9065668548', '', 'ТуЕг', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(204, 'Александр', 'Токач', '', 'мама9103221771, 9103641033', '', 'ТоАл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(205, 'Наталья', 'Цыбенко', '', '9102206629', '', 'ЦыНа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(206, 'Марина', 'Цыкал', '', '9277440137, 9205921682', '', 'ЦыМа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(207, 'Алина', 'Ширшова', '', '9645813172, 9045300028', '', 'ШиАл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(208, 'Вадим', 'Пелихов', '', '9205626488', '', 'ПеВа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(209, 'Михаил', 'Лагуткин', '', '9524398242', '', 'ЛаМи', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(210, 'Валентин', 'Слюсарев', '', '9040909913', '', 'СлВа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(211, 'Аким', 'Дмитриев', '', '9511411474', '', 'ДмАк', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(212, 'Валерий', 'Нерсисян', '', '9803738628', '', 'НеВа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(213, 'Григорий', 'Орлов', '', '9103609153', '', 'ОрГр', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(214, 'Санчес', 'Матеус', '', '9205967925', '', 'МаСа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(215, 'Владислав', 'Литвинов', '', '9205559706, мама 9205843920', '', 'ЛиВл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(216, 'Илья', 'Наумов', '', '9202046538', '', 'НаИл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(217, 'Дарина', 'Леонова', '', '9202009909', '', 'ЛеДа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(218, 'Алиса', 'Кудрявцева', '', '9517600055', '', 'КуАл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(219, 'Александр', 'Кравченко', '', '9205556546', '', 'КрАл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(220, 'Кирилл', 'Гнаповский', '', '9103667703', '', 'ГнКи', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(221, 'Дарья', 'Остапенко', '', '9202079686, мама 9524312072', '', 'ОсДа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(222, 'Василий', 'Девятов', '', '9102267886, 9205699500', '', 'ДеВа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(223, 'Ярослав', 'Истрашкин', '', '9517692499мама', '', 'ИсЯр', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(224, 'Арсений', 'Коренев', '', 'водитель 9103206069', '', 'КоАр', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(225, 'Дмитрий', 'Климов', '', '9511525097', '', 'КлДм', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(226, 'Ульяна', 'Егорова', '', '9155716692', '', 'ЕгУл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(227, 'Дарья', 'Дадыка', '', 'вк', '', 'ДаДа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(228, 'Александр', 'Гамза', '', '9125544369', '', 'ГаАл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(229, 'Валерия', 'Гончарова', '', '9155254048', '', 'ГоВа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(230, 'Павел', 'Шуленин', '', 'мама9205698623, 9205733776', '', 'ШуПа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(232, 'Валерий', 'Бескровный', '', '9524231323', '', 'БеВа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(233, 'Татьяна', 'Орлова', '', '9056704351', '', 'ОрТа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(234, 'Вера', 'Мартынюк', '', '9045303768папа 89040929827', '', 'МаВе', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(235, 'Ирина', 'Овсиенко', '', '9517613513', '', 'ОвИр', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(236, 'Дмитрий', 'Бутенко', '', '9205541023', '', 'БуДм', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(237, 'Артём', 'Серёгин', '', '', '', 'СеАр', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(238, 'Иван', 'Дементьев', '', '9205925424', '', 'ДеИв', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(241, 'Вячеслав', 'Раков', '', '+79107366746', '', 'РаВя', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(242, 'Михаил', 'Михайленко', '', '+79087801127', '', 'МиМи', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(243, 'Светлана', 'Свидовская', '', '9511363580', '', 'СвСв', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(244, 'Станислав', 'Звонов', '', '9803711540, 9066009119', '', 'ЗвСт', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(245, 'Егор', 'Крючков', '', '9202056051', '', 'КрЕг', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(246, 'Виталий', 'Макеев', '', '9065663579', '', 'МВ', 'd93591bdf7860e1e4ee2fca799911215', 4, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(247, 'Герман', 'Семёнов', '', '9102204713', '', 'СеГе', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(248, 'Ирина', 'Бойченко', '', '9148554126', '', 'БоИр', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(249, 'Андрей', 'Ракитин', '', '9205720150', '', 'РаАн', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(250, 'Дмитрий', 'Лебеденко', '', '9066059995', '', 'ЛеДм', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(251, 'Денис', 'Гнездилов', '', '9192243506', '', 'ГнДе', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(252, 'София', 'Полтева', '', '', '', 'ПолСо', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(253, 'Оксана', 'Полтева', '', '', '', 'ПолОк', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(254, 'сультация', 'кон', '', '', '', 'консул', '9ab436ca9482f1cfcaee53cc1a6ab7c1', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(255, 'Илья', 'Солдаткин ', '', '9803886673', '', 'СоИл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(256, 'Ярослав', 'Лебеденко', '', '9038860555', '', 'ЛеЯр', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(257, 'Виктория', 'Будько', '', '9087828685', '', 'БуВи', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(258, 'Игорь', 'Качук', '', '9030246755', '', 'КаИг', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(259, 'Лусине', 'Шахвердян', '', '9102287787', '', 'ШаЛу', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(260, 'Вадим', 'Юдин', '', '9103231177', '', 'ЮдВа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(261, 'Елена', 'Щетинина', '', '9036420233', '', 'ЩеЕл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(262, 'Подготовка', 'Само', '', '', '', 'Самопод', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(263, 'Маргарита', 'Харисова', '', '89155799809', '', 'ХаМа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(264, 'Алексей', 'Лагутин ', '', '89192221177', '', 'ЛаАл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(265, 'Елизавета', 'Лагутина', '', '', '', 'ЛаЕл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(266, 'Тест', 'Тест', '', '', '', 'тест', '81dc9bdb52d04dc20036dbd8313ed055', 4, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(267, 'Андрей', 'Кисенко', '', '89606326740', '', 'КиАн', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(268, 'Ярослав', 'Гринякин', '', '89805211205мама 89087845511', '', 'ГрЯр', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(269, 'Максим', 'Москаленко', '', '8-951-766-02-77', '', 'МоМа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(270, 'Дмитрий', 'Чаблин', '', '', '', 'ЧаДм', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(271, 'Максим', 'Перепелица', '', '9155600680', '', 'ПеМа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(272, 'Александр', 'Щипцов', '', '9202003900, папа9250429770', '', 'ЩиАл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(273, 'Подготовка', 'Само', '', '', '', 'Само', 'efbab39a1edd45d202bb5add9a9df753', 4, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(274, 'Павел', 'Логвинов', '', '89192871619 мама', '', 'НоПа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(275, 'Анна', 'Зинькова', '', '9102251201', '', 'ЗиАн', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(276, 'Марина', 'Субботина ', '', '89205536278', '', 'СуМа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(277, 'Виктория', 'Рвачева ', '', 'мама89194324808, папа9290000886, 9155742281', '', 'РвВи', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(278, 'Евгений', 'Петровский ', '', '89103244086 мама', '', 'ПеЕв', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(279, 'Лариса', 'Зырянова', '', '89045349415', '', 'ЗыЛа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(280, 'Виктория', 'Бугаева', '', ' 89103275764', '', 'БугВи', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(281, 'Владислав', 'Хрипунов', '', '89107451425', '', 'ХрВл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(282, 'Наталья', 'Глущенко ', '', '89507136447', '', 'ГлНа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(283, 'Артем', 'Ломакович', '', '89606254054мама', '', 'ЛоАр', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(284, 'Даниил', 'Матвеенко ', '', '89611727212', '', 'МаДа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(285, 'Мария', 'Рамазанова', '', '89103698305', '', 'РаМа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(286, 'Владислав', 'Гобелко', '', '9511594339', '', 'ГоВл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(287, 'Никита', 'Рузакин', '', '9511596862', '', 'РуНи', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(288, 'София', 'Степаненко', '', '9511581710мама, 9511582956бабушка', '', 'СтСо', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(289, 'Юлия', 'Белкина', '', '9803740377', '', 'БеЮл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(290, 'Татьяна', 'Камнева', '', '9040918558', '', 'КаТа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(291, 'Степан', 'Лисуненко', '', '9066083197, 9092006939', '', 'ЛиСт', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(292, 'Дарья', 'Курдюкова', '', '9103249006', '', 'КуДа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(293, 'Вероника', 'Варёшина', '', '89803756977', '', 'ВаВе', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(294, 'Татьяна', 'Шмелева', '', '9103269964', '', 'ШмТа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(295, 'Алексей', 'Бочаров', '', '', '', 'БоАл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(296, 'Вадим', 'Тихонов', '', '89038845555', '', 'ТиВа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(297, 'Евгения', 'Тулупова', '', '89066084042мама,  89606368035', '', 'ТуЕв', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(298, 'Артём', 'Филатов', '', '9103658036', '', 'ФиАр', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(299, 'Ольга', 'Гоменнюк ', '', '89202066147', '', 'ГоОл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(300, 'Ангелина', 'Статинова', '', '9507179331', '', 'СтАн', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(301, 'Галина', 'Салтыкова ', '', '89155255941', '', 'СаГа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(302, 'Павел', 'Харченко', '', '89192809111 89155255941', '', 'ХаПа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(303, 'Анастасия', 'Федорова ', '', '9065668812', '', 'ФеАн', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(304, 'Валерия', 'Журакова', '', 'валерия9517665598, 9040985488, 9507129046', '', 'ЖуВа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(305, 'Виолетта', 'Шапошникова', '', '9103207761мама', '', 'ШаВи', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(306, 'Александра', 'Мочалова', '', '', '', 'МоАл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(307, 'Владислава', 'Котельникова', '', '9066085215', '', 'КоВл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(308, 'Павел', 'Котельников', '', '9066085215', '', 'КоПа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(309, 'Виталий', 'Чепурных', '', '9040857580', '', 'ЧеВи', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(310, 'Станислав', 'Лукаш', '', '89092029210', '', 'СтЛу', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(311, 'Илья', 'Гудов', '', '9092018952', '', 'ГуИл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(312, 'Ирина', 'Витковская', '', '9155646210', '', 'ВиИр', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(313, 'Карина', 'Власенко', '', '89194322588', '', 'ВлКа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(314, 'Николай', 'Бурлаков', '', '9205853425', '', 'БуНи', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(315, 'Руслан', 'Исмаинов', '', '9103615681', '', 'ИсРу', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(316, 'Эльвира', 'Алексеенко', '', '9205592799', '', 'АлЭл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(317, 'Никита', 'Калинский', '', '89103262850', '', 'КаНи', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(318, 'Алина', 'Смирнова', '', '89803826884', '', 'СмАл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(319, 'Евгения', 'Шеховонова ', '', '89051733534', '', 'ШеЕв', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(320, 'Галина', 'Синегубова ', '', '89045305856', '', 'СиГа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(321, 'Марина', 'Овсянникова', '', '89192293607', '', 'ОвМа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(322, 'Анастасия', 'Присяжнюк', '', '89524319720', '', 'ПрАн', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(323, 'Полина', 'Котова', '', 'мама9045300066, 9045307959', '', 'КотПо', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(324, 'Тимофей', 'Воронцов', '', '9155627706', '', 'ВоТи', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 0, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(325, 'Лилия', 'Сапельник', '', '', '', 'СаЛи', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(326, 'Илья', 'Каленик', '', '89155799510', '', 'КаИл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(327, 'Виолетта', 'Яркова', '', '89087805623', '', 'ЯрВи', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(328, 'Владимир', 'Дуюн', '', '9040882772', '', 'ДуВл', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(329, 'Евгений', 'Лобанов', '', '9606300001', '', 'ЛоЕв', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(330, 'Татьяна', 'Рябухина', '', '9103623751, папа9040962985', '', 'РяТа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(331, 'Анастасия', 'Петкевич', '', '9205968524', '', 'ПеАн', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(332, 'Людмила', 'Романова', '', '9066068213', '', 'РоЛю', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(333, 'Лариса', 'Малявина', '', '', '', 'МаЛа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(334, 'Виктория', 'Глебова', '', '89040890974', '', 'ГлВи', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(335, 'Галина', 'Кривопускова', '', '89205715555', '', 'КрГа', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(336, 'Иван', 'Козицкий', '', '9103690506мама, 9103690702', '', 'КоИв', '81dc9bdb52d04dc20036dbd8313ed055', 5, '2018-03-20', 1, 0, 'a:3:{i:0;i:7;i:1;i:8;i:2;i:9;}'),
(338, 'Олег', 'Галицин', 'Владимирович', '8-800-555-35-35', '', 'oleg', '4a7d1ed414474e4033ac29ccb8653d9b', 2, '2018-03-21', 1, 1, 'b:0;');

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
-- Индексы таблицы `Property_String`
--
ALTER TABLE `Property_String`
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;
--
-- AUTO_INCREMENT для таблицы `Admin_Form_Modelname`
--
ALTER TABLE `Admin_Form_Modelname`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT для таблицы `Admin_Form_Type`
--
ALTER TABLE `Admin_Form_Type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT для таблицы `Admin_Menu`
--
ALTER TABLE `Admin_Menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT для таблицы `Constant`
--
ALTER TABLE `Constant`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `Property_Int`
--
ALTER TABLE `Property_Int`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=480;
--
-- AUTO_INCREMENT для таблицы `Property_List`
--
ALTER TABLE `Property_List`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=255;
--
-- AUTO_INCREMENT для таблицы `Property_List_Values`
--
ALTER TABLE `Property_List_Values`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
--
-- AUTO_INCREMENT для таблицы `Property_String`
--
ALTER TABLE `Property_String`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;
--
-- AUTO_INCREMENT для таблицы `Property_Text`
--
ALTER TABLE `Property_Text`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
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
