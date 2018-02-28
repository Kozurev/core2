<?php

$oUser = Core::factory('User');

if(isset($_POST['login_in']) && isset($_POST['login']) && isset($_POST['password']))
{
	$login = $_POST['login'];
	$pass = $_POST['password'];
	$oUser->authorize();
}
