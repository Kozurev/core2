<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 20.04.2018
 * Time: 1:02
 */

// $dbh = new mysqli("37.140.192.32:3306", "u4834_ADMIN", "big#psKT", "u4834955_musbase");
// $dbh->query("SET NAMES utf8");

// Core::factory("Orm")->executeQuery("TRUNCATE Task");
// Core::factory("Orm")->executeQuery("TRUNCATE Task_Note");

// $aoTasks = $dbh->query("SELECT * FROM `admin_notes` WHERE date >= '2018-01-01'");

// while ($task = $aoTasks->fetch_object())
// {
//     $oTask = Core::factory("Task")
//         ->date($task->date)
//         ->type($task->type + 1)
//         ->done($task->done);

//     $oTask = $oTask->save();

//     $oTask->addNote($task->note);
// }


// Core::factory("Orm")->executeQuery("TRUNCATE Lid");
// Core::factory("Orm")->executeQuery("TRUNCATE Lid_Comment");
// Core::factory("Orm")->executeQuery("DELETE FROM Property_List WHERE model_name = 'Lid'");
// Core::factory("Orm")->executeQuery("DELETE FROM Property_List_Assigment WHERE model_name = 'Lid'");

// $aoLids = $dbh->query("SELECT * FROM `lids`");

// while ($lid = $aoLids->fetch_object())
// {
// 	$Lid = Core::factory("Lid")
// 		->name($lid->name)
// 		->surname($lid->surname)
// 		->number($lid->phone)
// 		->vk($lid->vk)
// 		->controlDate($lid->date);

// 	$Lid->save();

// 	Core::factory("Property", 27)->addNewValue($Lid, $lid->status + 79);

// 	Core::factory("Lid_Comment")
// 		->lidId($Lid->getId())
// 		->text($lid->note)
// 		->save();
// }





Core::factory( "User_Controller_Show" )
	->properties( array(12, 13, 14, 15, 16, 18, 19) )
	->limit( 15 )
	->xsl( "musadm/users/clients_new.xsl" )
	->show();