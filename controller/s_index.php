<?php

$cookieData = "1";

$cookieTime = 3600 * 24;
if(REMEMBER_USER_TIME != "")    $cookieTime *= REMEMBER_USER_TIME;

//setcookie("user_data", $cookieData, time() + $cookieTime);

//setcookie("user_data", "Hello world", time() - 3600);