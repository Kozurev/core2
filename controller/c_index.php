<?php

//    $musdb = new PDO("mysql:host=localhost;dbname=musadm", "root", "");
//
//    $data = $musdb->query("SELECT * from role_assignment where userid > 40");
//    $data->setFetchMode(PDO::FETCH_OBJ);
//    $users = $data->fetchAll();
//
//    echo "<pre>";
//    print_r($users);
//
//    foreach ($users as $user)
//    {
//
//    }

echo "<pre>";

$arr = array("first" => 1, "second" => 2, "third" => 3);

print_r($arr);
var_dump(Core_Array::unsetValue($arr, "second"));
