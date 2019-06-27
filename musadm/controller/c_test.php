<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 20.04.2018
 * Time: 1:02
 */

//$dbh = new mysqli("37.140.192.32:3306", "u4834_root", "n1omY2_1", "u4834955_core");
//$dbh->query("SET NAMES utf8");
Orm::Debug(true);
$Orm = new Orm();

//Core_Access::instance()->install();
//$Orm->executeQuery('DELETE FROM Structure WHERE path=\'access\'');
//Core::factory('Structure')
//    ->title('Права доступа')
//    ->parentId(0)
//    ->path('access')
//    ->action('musadm/access/index')
//    ->templateId(10)
//    ->save();
//$Orm->executeQuery('UPDATE User SET subordinated = id WHERE group_id = 6');

$Orm->executeQuery('ALTER TABLE Payment ADD author_id int DEFAULT 0 NULL');
$Orm->executeQuery('ALTER TABLE Payment ADD author_fio varchar(255) DEFAULT \'\' NULL;');