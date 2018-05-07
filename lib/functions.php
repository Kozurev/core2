<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 05.05.2018
 * Time: 19:59
 */


function debug( $val )
{
    echo "<pre>";
    print_r($val);
    echo "</pre>";
}

/**
 * Функция сложения времени
 *
 * @param $time - исходное время
 * @param $val - прибавляемое значение
 * @param $type - к чему будет прибавляться значние (минуты или часы)
 * @return string
 */
function addTime( $time, $val )
{
    $aSegments1 = explode(":", $time);
    $aSegments2 = explode(":", $val);

    $hours1 = intval( $aSegments1[0] );
    $hours2 = intval( $aSegments2[0] );
    $minutes1 = intval( $aSegments1[1] );
    $minutes2 = intval( $aSegments2[1] );
    $seconds1 = intval( $aSegments1[2] );
    $seconds2 = intval( $aSegments2[2] );

    $seconds = $seconds1 + $seconds2;
    if( $seconds > 60 )
    {
        $minutes2 += intval( $seconds / 60 );
        $seconds = intval( $seconds % 60 );
    }
    if( $seconds < 10 ) $seconds = "0" . $seconds;
    if( $seconds == 60 )
    {
        $seconds = "00";
        $minutes1++;
    }

    $minutes = $minutes1 + $minutes2;
    if( $minutes > 60 )
    {
        $hours1 += intval( $minutes / 60 );
        $minutes = intval( $minutes % 60 );
    }
    if( $minutes < 10 ) $minutes = "0" . $minutes;
    if( $minutes == 60 )
    {
        $minutes = "00";
        $hours1++;
    }

    $hours = $hours1 + $hours2;
    if( $hours < 10 )   $hours = "0" . $hours;

    $aSegments[] = $hours;
    $aSegments[] = $minutes;
    $aSegments[] = $seconds;

    return implode(":", $aSegments);
}


function deductTime( $time1, $time2 )
{
    $aSegments1 = explode(":", $time1);
    $aSegments2 = explode(":", $time2);

    $hours1 = intval( $aSegments1[0] );
    $hours2 = intval( $aSegments2[0] );
    $minutes1 = intval( $aSegments1[1] );
    $minutes2 = intval( $aSegments2[1] );
    $seconds1 = intval( $aSegments1[2] );
    $seconds2 = intval( $aSegments2[2] );

    $seconds = $seconds1 - $seconds2;
    if( $seconds < 0 )
    {
        $seconds = 60 - $seconds;
        $minutes1--;
    }
    if( $seconds < 10 ) $seconds = "0" . $seconds;

    $minutes = $minutes1 - $minutes2;
    if( $minutes < 0 )
    {
        $minutes = 60 + $minutes;
        $hours1--;
    }
    if( $minutes < 10 ) $minutes = "0" . $minutes;

    $hours = $hours1 - $hours2;

    $aSegments[] = $hours;
    $aSegments[] = $minutes;
    $aSegments[] = $seconds;

    return implode(":", $aSegments);
}


function getMinutes( $time1 )
{
    $aSegments = explode(":", $time1);
    $minutes = intval( $aSegments[1] );
    return $minutes;
}


function getHours( $time )
{
    $aSegments = explode(":", $time);
    $minutes = intval( $aSegments[0] );
    return $minutes;
}

/**
 * Сравнение времени
 *
 * @param $time1
 * @param $time2
 * @return bool; true - если первое значение больше второго
 */
function compareTime( $time1, $time2 )
{
    $aTime1 = explode( ":", $time1 );
    $aTime2 = explode( ":", $time2 );

    $hours1 = intval( $aTime1[0] );
    $hours2 = intval( $aTime2[0] );

    $minutes1 = intval( $aTime1[1] );
    $minutes2 = intval( $aTime2[1] );

    $seconds1 = intval( $aTime1[2] );
    $seconds2 = intval( $aTime2[2] );

    //if( $time1 == $time2 )          return false;
    if( $hours1 > $hours2 )         return true;
    elseif( $hours1 < $hours2 )     return false;
    elseif( $minutes1 > $minutes2 ) return true;
    elseif( $minutes1 < $minutes2 ) return false;
    elseif( $seconds1 > $seconds2 ) return true;
    elseif( $seconds1 < $seconds2 ) return false;

    return true;
}

function refactorTimeFormat( $time )
{
    $aSegments = explode(":", $time);
    $result = $aSegments[0] . ":" . $aSegments[1];
    return $result;
}