<?php
$director = User_Auth::current();
$apiUrl = Property_Controller::factoryByTag('my_calls_url')->getValues($director)[0]->value();
$apiToken = Property_Controller::factoryByTag('my_calls_token')->getValues($director)[0]->value();

(new Core_Entity())
    ->addSimpleEntity('api_url', $apiUrl)
    ->addSimpleEntity('api_token', $apiToken)
    ->addEntity($director, 'director')
    ->xsl('musadm/integration/myCalls/api_url.xsl')
    ->show();