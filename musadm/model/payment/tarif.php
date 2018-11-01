<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 28.04.2018
 * Time: 16:07
 */

class Payment_Tarif extends Payment_Tarif_Model
{


    public function save()
    {
        Core::notify( array( &$this ), "beforePaymentTarifSave" );
        parent::save();
        Core::notify( array( &$this ), "afterPaymentTarifSave" );
    }


    public function delete( $obj = null )
    {
        Core::notify( array( &$this ), "beforePaymentTarifDelete" );
        parent::delete();
        Core::notify( array( &$this ), "afterPaymentTarifDelete" );
    }


}