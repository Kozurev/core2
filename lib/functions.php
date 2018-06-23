<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 05.05.2018
 * Time: 19:59
 */


function debug( $val, $type = 0 )
{
    echo "<pre>";
    if( $type == false )    print_r($val);
    else                    var_dump($val);
    echo "</pre>";
}


/**
 * Сложение времени
 *
 * @param $time - исходное время
 * @param $val - прибавляемое значение
 * @param $type - к чему будет прибавляться значние (минуты или часы)
 * @return string
 */
function addTime( $time, $val )
{
    $result = toSeconds( $time ) + toSeconds( $val );
    return toTime( $result );
}


/**
 * Вычитание времени
 *
 * @param $time1
 * @param $time2
 * @return string
 */
function deductTime( $time1, $time2 )
{
    $totalCountSeconds1 = toSeconds( $time1 );
    $totalCountSeconds2 = toSeconds( $time2 );
    return toTime($totalCountSeconds1 - $totalCountSeconds2);
}


/**
 * Сравнение времени
 *
 * @param $time1 - сравниваемое значение
 * @param $time2 - сравниваемое значение
 * @param $condition - условие сравнения
 * @return bool; true - если первое значение больше второго
 */
function compareTime( $time1, $condition, $time2 )
{
    $totalCountSeconds1 = toSeconds( $time1 );
    $totalCountSeconds2 = toSeconds( $time2 );

    switch ( $condition )
    {
        case ">":
        {
            if( $totalCountSeconds1 > $totalCountSeconds2 )   return true;
            else    return false;
        }
        case ">=":
        {
            if( $totalCountSeconds1 >= $totalCountSeconds2 )  return true;
            else    return false;
        }
        case "<":
        {
            if( $totalCountSeconds1 < $totalCountSeconds2 )   return true;
            else    return false;
        }
        case "<=":
        {
            if( $totalCountSeconds1 <= $totalCountSeconds2 )  return true;
            else    return false;
        }
        case "==":
        {
            if( $totalCountSeconds1 == $totalCountSeconds2 )  return true;
            else    return false;
        }
        default: return false;
    }
}


/**
 * Деление времени
 *
 * @param $time1
 * @param $time2
 * @param $divType - тип деления ('/' или '%')
 * @return int
 */
function divTime( $time1, $time2, $divType )
{
    $totalCountSeconds1 = toSeconds( $time1 );
    $totalCountSeconds2 = toSeconds( $time2 );

    if( $divType == "/" )       return intval( $totalCountSeconds1 / $totalCountSeconds2 );
    elseif ( $divType == "%" )  return intval( $totalCountSeconds1 % $totalCountSeconds2 );
}


/**
 * Перевод количества секунд во время (H:i:s)
 *
 * @param $seconds - кол-во секунд
 * @return string
 */
function toTime( $seconds )
{
    $hours = intval($seconds / (60 * 60));
    $seconds -= intval( $hours * 60 * 60 );

    $minutes = intval( $seconds / 60 );
    $seconds -= intval( $minutes * 60 );

    $aSegments = array();

    if($hours < 10)     $hours = "0" . $hours;
    if($minutes < 10)   $minutes = "0" . $minutes;
    if($seconds < 10)   $seconds = "0" . $seconds;

    $aSegments[] = $hours;
    $aSegments[] = $minutes;
    $aSegments[] = $seconds;

    return implode(":", $aSegments);
}


/**
 * Преобразование времени в количество секунд
 *
 * @param $time
 * @return float|int
 */
function toSeconds( $time )
{
    $aSegments = explode(":", $time);

    if(!is_array($aSegments) || count($aSegments) < 3) return "";

    $hours =    intval( $aSegments[0] );
    $minutes =  intval( $aSegments[1] );
    $seconds =  intval( $aSegments[2] );

    $totalCountSeconds =    $hours * 60 * 60;
    $totalCountSeconds +=   $minutes * 60;
    $totalCountSeconds +=   $seconds;

    return $totalCountSeconds;
}


function refactorTimeFormat( $time )
{
    $aSegments = explode(":", $time);
    $result = $aSegments[0] . ":" . $aSegments[1];
    return $result;
}


function refactorDateFormat ( $date, $glue = ".", $type = "full")
{
    $aSegments = explode("-", $date);
    if( $type === "short" ) unset($aSegments[0]);
    $aSegments = array_reverse($aSegments);
    return implode($glue, $aSegments);
}


function getMonth($date)
{
    $month = substr( $date, 5 );
    $month = intval( substr( $month, 0, 3 ) );
    return $month;
}


function getYear($date)
{
    return substr($date, 0, 4);
}


function getMonthName($date)
{
    $mouthes = array("Январь", "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь");
    return $mouthes[getMonth($date) - 1];
}