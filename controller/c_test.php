<?php
/**
 * Created by PhpStorm.
 * User: Egor
 * Date: 20.04.2018
 * Time: 1:02
 */

//$dbh = new mysqli("37.140.192.32:3306", "u4834_ADMIN", "big#psKT", "u4834955_musbase");
//$dbh->query("SET NAMES utf8");

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


$time1 = "16:30:00";
$time2 = "17:25:00";
$period = "00:15:00";


$minutes = deductTime( $time2, $time1 );
$rowspan = divTime( $minutes, $period, "/" );
if( divTime( $minutes, $period, "%" ) ) $rowspan++;

//$time = deductTime( $time2, $time1 );
var_dump($rowspan);

