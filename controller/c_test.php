<?php
/**
 * Created by PhpStorm.
 * User: Egor
 * Date: 20.04.2018
 * Time: 1:02
 */

$dbh = new mysqli("37.140.192.32:3306", "u4834_ADMIN", "big#psKT", "u4834955_musbase");
$dbh->query("SET NAMES utf8");

Core::factory("Orm")->executeQuery("TRUNCATE `Lid`");
//Core::factory("Orm")->executeQuery("TRUNCATE `Lid_Comment`");
//
//$aoLids = Core::factory("Lid")->findAll();
//$oProperty = Core::factory("Property");
//foreach ($aoLids as $lid) $oProperty->clearForObject($lid);
//
//
//
//$aoLids = $dbh->query("SELECT * FROM lids");
//$oProperty = Core::factory("Property", 27);
//
//while($lid = $aoLids->fetch_object())
//{
//    $oLid = Core::factory("Lid")
//        ->name($lid->name)
//        ->surname($lid->surname)
//        ->number($lid->phone)
//        ->vk($lid->vk);
//
//    $oLid->save();
//
//    $oProperty->addNewValue($oLid, intval($lid->status) + 79);
//
//    Core::factory("Lid_Comment")
//        ->authorId(1)
//        ->lidId($oLid->getId())
//        ->text($lid->note)
//        ->save();
//}