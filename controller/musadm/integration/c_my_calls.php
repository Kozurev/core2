<?php
$director = User_Auth::current();
$apiUrl = Property_Controller::factoryByTag('my_calls_url')->getValues($director)[0]->value();

(new Core_Entity())
    ->addSimpleEntity('api_url', $apiUrl)
    ->addEntity($director, 'director')
    ->xsl('musadm/integration/myCalls/api_url.xsl')
    ->show();