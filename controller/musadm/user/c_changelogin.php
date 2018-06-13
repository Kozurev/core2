<?php

$oUser = Core::factory("User")->getCurrent();

Core::factory("Core_Entity")
	->addEntity($oUser)
	->xsl("musadm/users/changelogin.xsl")
	->show();