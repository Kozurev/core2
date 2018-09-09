<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 20.04.2018
 * Time: 15:05
 */

class Payment extends Payment_Model
{

    public function getUser()
    {
        $User = Core::factory("User", $this->user);

        if( $User != false )    return $User;
        else    return Core::factory( "User" );
    }

    public function save($obj = null)
    {
        Core::notify(array(&$this), "beforePaymentSave");
        if($this->datetime == "")   $this->datetime = date("Y-m-d");
        parent::save();
        Core::notify(array(&$this), "afterPaymentSave");
    }


    public function delete($obj = null)
    {
        Core::notify(array(&$this), "beforePaymentDelete");
        parent::delete();
        Core::notify(array(&$this), "beforePaymentDelete");
    }

}