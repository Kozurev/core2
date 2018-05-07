<?php
/**
 * Created by PhpStorm.
 * User: Egor
 * Date: 20.04.2018
 * Time: 1:02
 */

$dbh = new mysqli("37.140.192.32:3306", "u4834_ADMIN", "big#psKT", "u4834955_musbase");
$dbh->query("SET NAMES utf8");

//Core::factory("Orm")->executeQuery("TRUNCATE Payment_Tarif");
//$aoTarifs = $dbh->query("SELECT * FROM `ref_packet`");
//
//while ($tarif = $aoTarifs->fetch_object())
//{
//    Core::factory("Payment_Tarif")
//        ->title($tarif->name)
//        ->price($tarif->price)
//        ->lessonsCount($tarif->numberlesson)
//        ->lessonsType($tarif->typelessonid)
//        ->access(!intval($tarif->onlyadmin))
//        ->save();
//}


$time1 = "10:00:00";
$time2 = "10:00:00";

$time = compareTime( $time1, $time2 );
var_dump($time);

