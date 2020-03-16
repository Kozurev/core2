<?php
/**
 * Created by PhpStorm.
 * User: Egor
 * Date: 14.03.2020
 * Time: 12:05
 */

$director = User_Auth::current()->getDirector();
$jivoActive = Property_Controller::factoryByTag('jivo_active')->getValues($director)[0]->value();
$jivoScript = Property_Controller::factoryByTag('jivo_script')->getValues($director)[0]->value();

(new Core_Entity())
    ->addSimpleEntity('jivo_active', $jivoActive)
    ->addSimpleEntity('jivo_script', $jivoScript)
    ->addEntity($director, 'director')
    ->xsl('musadm/integration/jivo/index.xsl')
    ->show();