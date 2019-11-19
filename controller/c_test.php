<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 20.04.2018
 * Time: 1:02
 */

//$dbh = new mysqli("37.140.192.32:3306", "u4834_root", "n1omY2_1", "u4834955_core");
//$dbh->query("SET NAMES utf8");
global $CFG;
Orm::Debug(false);
$Orm = new Orm();


$Orm->executeQuery('
    CREATE TABLE Schedule_Teacher
    (
        id int PRIMARY KEY AUTO_INCREMENT,
        teacher_id int,
        day_name varchar(10),
        time_from time,
        time_to time
    );
');