<?php
/**
 * Created by PhpStorm.
 * User: Egor
 * Date: 24.04.2018
 * Time: 22:10
 */

class Lid extends Lid_Model
{
    public function save($obj = null)
    {
        Core::notify(array(&$this), "beforeLidSave");
        parent::save();
        Core::notify(array(&$this), "afterLidSave");
    }
}