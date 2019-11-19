<?php
/**
 * Created by PhpStorm.
 *
 * @author BadWolf
 * @date 05.05.2018 19:59
 * @version 20190325
 */


/**
 * @param $val
 * @param bool $type
 */
function debug($val, bool $type = false)
{
    echo '<pre>';
    if ($type == false) {
        print_r($val);
    } elseif ($type == true) {
        var_dump($val);
    }
    echo '</pre>';
}


/**
 * Преобразовывает строку из snake_case в camelCase
 *
 * @param string $convertingString
 * @return string
 */
function toCamelCase(string $convertingString) : string
{
    $return = '';
    $words = explode('_', $convertingString);
    foreach ($words as $word) {
        $return .= ucfirst($word);
    }
    return lcfirst($return);
}


/**
 * Сложение времени
 *
 * @param string $time - исходное время
 * @param string $val - прибавляемое значение
 * @return string
 */
function addTime(string $time, string $val) : string
{
    $result = toSeconds($time) + toSeconds($val);
    return toTime($result);
}


/**
 * Вычитание времени
 *
 * @param string $time1
 * @param string $time2
 * @return string
 */
function deductTime(string $time1, string $time2) : string
{
    $totalCountSeconds1 = toSeconds($time1);
    $totalCountSeconds2 = toSeconds($time2);
    return toTime($totalCountSeconds1 - $totalCountSeconds2);
}


/**
 * Сравнение времени
 *
 * @param string $time1 - сравниваемое значение
 * @param string $time2 - сравниваемое значение
 * @param string $condition - условие сравнения
 * @return bool; true - если первое значение больше второго
 */
function compareTime(string $time1, string $condition, string $time2) : bool
{
    $totalCountSeconds1 = toSeconds($time1);
    $totalCountSeconds2 = toSeconds($time2);

    switch ($condition)
    {
        case '>':
            if ($totalCountSeconds1 > $totalCountSeconds2) {
                return true;
            }
            else {
                return false;
            }

        case '>=':
            if ($totalCountSeconds1 >= $totalCountSeconds2) {
                return true;
            }
            else {
                return false;
            }

        case '<':
            if ($totalCountSeconds1 < $totalCountSeconds2) {
                return true;
            }
            else {
                return false;
            }

        case '<=':
            if ($totalCountSeconds1 <= $totalCountSeconds2) {
                return true;
            }
            else {
                return false;
            }

        case '==':
            if ($totalCountSeconds1 == $totalCountSeconds2) {
                return true;
            }
            else {
                return false;
            }

        default: return false;
    }
}


/**
 * Проверка на принадлежность времени $time временному промежутку с $rangeStart по $rangeEnd
 * параметр $inclusive является казателем на временной промежуток с ... по .. ВКЛЮЧИТЕЛЬНО
 *
 * @param string $time
 * @param string $rangeStart
 * @param string $rangeEnd
 * @param bool $inclusive
 * @return bool
 */
function isTimeInRange(string $time, string $rangeStart, string $rangeEnd, bool $inclusive = false) : bool
{
    $sec = toSeconds($time);
    $secR1 = toSeconds($rangeStart);
    $secR2 = toSeconds($rangeEnd);

    if ($inclusive === true) {
        if ($sec >= $secR1 && $sec <= $secR2) {
            return true;
        } else {
            return false;
        }
    } else {
        if ($sec > $secR1 && $sec < $secR2) {
            return true;
        } else {
            return false;
        }
    }
}


/**
 * Деление времени
 * ХЗ почему функция возвращает целочисленное значение, но так надо, иначе не работает
 *
 * @param string $time1
 * @param string $time2
 * @param string $divType - тип деления ('/' или '%')
 * @return int
 */
function divTime(string $time1, string $time2, string $divType) : int
{
    $totalCountSeconds1 = toSeconds($time1);
    $totalCountSeconds2 = toSeconds($time2);

    if ($divType == '/') {
        return intval($totalCountSeconds1 / $totalCountSeconds2);
    } elseif ($divType == '%') {
        return intval($totalCountSeconds1 % $totalCountSeconds2);
    } else {
        return 0;
    }
}


/**
 * Перевод количества секунд во время (H:i:s)
 *
 * @param $seconds - кол-во секунд
 * @return string
 */
function toTime($seconds) : string
{
    $hours = intval($seconds / (60 * 60));
    $seconds -= intval($hours * 60 * 60);
    $minutes = intval($seconds / 60);
    $seconds -= intval($minutes * 60);

    if ($hours < 10) {
        $hours = '0' . $hours;
    }
    if ($minutes < 10) {
        $minutes = '0' . $minutes;
    }
    if ($seconds < 10) {
        $seconds = '0' . $seconds;
    }

    $segments[] = $hours;
    $segments[] = $minutes;
    $segments[] = $seconds;
    return implode(':', $segments);
}


/**
 * Преобразование времени в количество секунд
 *
 * @param string $time
 * @return int
 */
function toSeconds(string $time) : int
{
    $segments = explode( ':', $time );

    if (!is_array($segments) || count($segments) < 3) {
        return '';
    }

    $hours =   intval($segments[0]);
    $minutes = intval($segments[1]);
    $seconds = intval($segments[2]);

    $totalCountSeconds =  $hours * 60 * 60;
    $totalCountSeconds += $minutes * 60;
    $totalCountSeconds += $seconds;
    return $totalCountSeconds;
}


/**
 * Преобразование веремени формата hh:ii:ss => hh:ii
 *
 * @param string $time - время
 * @return string
 */
function refactorTimeFormat(string $time) : string
{
    $segments = explode(':', $time);
    if (count($segments) != 3) {
        return $time;
    }
    $result = $segments[0] . ':' . $segments[1];
    return $result;
}


/**
 * Преобразование даты формата Y-m-d => d.m.Y
 * Знаю что можно решить данную задачу стандартными средствами PHP но мы ведь не ищем легких путей :D
 *
 * @param string $date - дата формата Y-m-d
 * @param string $glue - разделитель между элементами даты
 * @param string $type - тип: full - с годом (d.m.Y); short - без года (d.m)
 * @return string
 */
function refactorDateFormat($date, $glue = '.', $type = 'full')
{
    $type === 'short'
        ?   $format = 'd' . $glue . 'm'
        :   $format = 'd' . $glue . 'm' . $glue . 'y';

    return date($format, strtotime($date));
}


/**
 * Сравнение даты
 * Формат даты учитывается только для строгого сравнения "===" и "!=="
 * К примеру:
 *      "22.10.19" "==" "2019-10-22" --> true
 *      "22.10.19" "===" "2019-10-22" --> false
 *
 * @param string $date1
 * @param string $condition
 * @param string $date2
 * @return bool
 */
function compareDate($date1, $condition, $date2)
{
    if (empty($date1) || empty($condition) || empty($date2)) {
        return false;
    }

    $time1 = strtotime($date1);
    $time2 = strtotime($date2);

    switch ($condition)
    {
        case '==':  return $time1 == $time2;
        case '!=':  return $time1 != $time2;
        case '>':   return $time1 > $time2;
        case '>=':  return $time1 >= $time2;
        case '<':   return $time1 < $time2;
        case '<=':  return $time1 <= $time2;
        case '===': return $date1 === $date2;
        case '!==': return $date1 !== $date2;
        default:    return false;
    }
}


/**
 * Получение номера месяца
 *
 * @param string $date - дата формата Y-m-d
 * @return int
 */
function getMonth($date) : int
{
    $month = substr($date, 5);
    $month = substr($month, 0, 3 );
    return intval($month);
}


/**
 * Получение номера года
 *
 * @param string $date - дата формата Y-m-d
 * @return int
 */
function getYear($date) : int
{
    return intval(substr($date, 0, 4));
}


/**
 * Получение названия месяца из даты
 *
 * @param string $date - дата формата 'Y-m-d'
 * @return string - название месяца
 */
function getMonthName($date)
{
    $months = ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'];
    return $months[getMonth($date) - 1];
}


/**
 * Преобразование строки русских символов в английские
 *
 * @param string $str - исходная строка
 * @return string
 */
function translite($str)
{
    $result = '';
    $str = trim($str);

    $translite = [
        'а' => 'a', 'б' => 'b', 'в' => 'v',
        'г' => 'g', 'д' => 'd', 'е' => 'e',
        'ё' => 'y', 'ж' => 'j', 'з' => 'z',
        'и' => 'i', 'й' => 'j', 'к' => 'k',
        'л' => 'l', 'м' => 'm', 'н' => 'n',
        'о' => 'o', 'п' => 'p', 'р' => 'r',
        'с' => 's', 'т' => 't', 'у' => 'u',
        'ф' => 'f', 'х' => 'h', 'ц' => 'c',
        'ч' => 'ch', 'ш' => 'sh','щ' => 'sh',
        'ъ' => '', 'ы' => 'u', 'ь' => '',
        'э' => 'e', 'ю' => 'ju', 'я' => 'ja'
    ];

    while ($str !== false && iconv_strlen($str) > 0) {
        $temp = mb_substr($str, 0, 1);
        $temp = mb_strtolower($temp);

        if ($temp == ' ') {
            $result .= '-';
        } elseif (is_numeric($temp)) {
            $result .= $temp;
        } elseif (in_array($temp, array_keys($translite))) {
            if (Core_Array::getValue($translite, $temp, false) !== false) {
                $result .= $translite[$temp];
            }
        } else {
            $result .= $temp;
        }

        $str = mb_substr($str, 1);
    }

    return $result;
}


/**
 * Определяет, является ли строка датой формата "Y-m-d"
 *
 * @date 20.09.2019 18:14
 * @param string $str
 * @return bool
 */
function isDate(string $str) : bool
{
    return preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', $str);
}


/**
 * Опедеяет, является ли строка временем формата "H:i:s"
 *
 * @date 20.09.2019 18:14
 * @param string $str
 * @return bool
 */
function isTime(string $str) : bool
{
    return preg_match('/^(00|[0-9]|1[0-9]|2[0-3]):([0-9]|[0-5][0-9]):([0-9]|[0-5][0-9])$/', $str);
}


/**
 *
 * @date 19.11.2019 16:09
 * @param $day
 * @return bool
 */
function isDayName($day) : bool
{
    $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
    return in_array($day, $days);
}


/**
 * Определяет, является ли строка датой и временем формата "Y-m-d H:i:s"
 *
 * @date 20.09.2019 18:14
 * @param string $str
 * @return bool
 */
function isDatetime(string $str) : bool
{
    return preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1]) (00|[0-9]|1[0-9]|2[0-3]):([0-9]|[0-5][0-9]):([0-9]|[0-5][0-9])$/', $str);
}


/**
 * Функция генерации случайной уникальной строки
 *
 * @param int $length
 * @return bool|string
 */
function uniqidReal($length = 13) : string
{
    // uniqid gives 13 chars, but you could adjust it to your needs.
    if (function_exists('random_bytes')) {
        $bytes = random_bytes(ceil($length / 2));
    } elseif (function_exists('openssl_random_pseudo_bytes')) {
        $bytes = openssl_random_pseudo_bytes(ceil($length / 2));
    } else {
        return uniqid();
    }
    return substr(bin2hex($bytes), 0, $length);
}


/**
 * Функция для проверки авторизации пользователя и если он не авторизован то редирект на страницу авторизации
 */
function authOrOut()
{
    if (empty(User_Auth::current())) {
        header('Location: ' . mapping('auth', ['back' => $_SERVER['REQUEST_URI']]));
        exit;
    }
}


/**
 * Функция для определения адреса страницы по её названию
 *
 * @param string $route
 * @param array $params
 * @return string
 */
function mapping(string $route, $params = []) : string
{
    global $CFG;

    $mapping = [
        'auth' => 'authorize'
    ];

    $getParams = !empty($params)
        ?   '?' . http_build_query($params)
        :   '';

    return $CFG->wwwroot . '/' . $mapping[$route] . $getParams ?? '';
}