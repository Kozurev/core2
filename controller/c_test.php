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

?>

<style>
    ul {
        margin-left: 0; /* Отступ слева в браузере IE и Opera */
        padding-left: 0; /* Отступ слева в браузере Firefox, Safari, Chrome */
    }

    .dropdown {
        position: absolute;
        display: none;
        //background: #555;
    }

    .submenu {
        position:relative;
        display:inline-block;
    }

    .submenu li {
        list-style-type: none;
    }

    .submenu li:hover > ul {
        display: block;
    }

    .submenu .dropdown li a {
        display: block;
        padding: 5px;
        text-decoration: none;
        color: #666;
        border: 1px solid #ccc;
        background-color: #f0f0f0;
        border-bottom: none;
    }

    .submenu .dropdown li a:hover {
        color: #ffe;
        background-color: #5488af;
    }
</style>


<ul class="submenu">
    <li>
        <a href="#">Пример</a>
        <ul class="dropdown">
            <li><a href="#">uygerth</a></li>
            <li><a href="#">uygerth</a></li>
            <li><a href="#">uygerth</a></li>
            <li><a href="#">uygerth</a></li>
            <li><a href="#">uygerth</a></li>
        </ul>
    </li>
</ul>

<?
$oUser = Core::factory("User", 297);
$oProperty = Core::factory("Property", 9);

echo "<pre>";
print_r($oProperty->getPropertyValues($oUser));
echo "</pre>";