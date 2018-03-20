<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 20.03.2018
 * Time: 16:03
 */

class Core_Array
{
    public static function getValue($arr, $key, $default)
    {
        if(isset($arr[$key]) && $arr[$key] != "")
        {
            return $arr[$key];
        }
        else return $default;
    }
}