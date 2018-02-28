<?php

/*
*	Обработчик выхода из учетной записи
*/
if(isset($_GET["disauthorize"]))
{
	$oUser = Core::factory("User");
	$oUser::disauthorize();
}

if(isset($_GET["ajax"]) && $_GET["ajax"] == 1)
{
	$this->execute();
	exit;
}