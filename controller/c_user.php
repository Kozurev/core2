<?php
unset($_SESSION['core']['user']);
$oUser = User::getCurent();
echo "<pre>"; print_r($oUser); echo "</pre>";



