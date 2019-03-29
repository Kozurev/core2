<?php
/**
 * Класс реализующий методы для работы с тарифом
 *
 * @author BadWolf
 * @date 28.04.2018 16:07
 * @version 20190328
 * Class Payment_Tarif
 */
class Payment_Tarif extends Payment_Tarif_Model
{
    /**
     * @param null $obj
     * @return $this|void
     */
    public function save($obj = null)
    {
        Core::notify([&$this], 'beforePaymentTarifSave');
        parent::save();
        Core::notify([&$this], 'afterPaymentTarifSave');
    }


    /**
     * @param null $obj
     * @return $this|void
     */
    public function delete($obj = null)
    {
        Core::notify([&$this], 'beforePaymentTarifDelete');
        parent::delete();
        Core::notify([&$this], 'afterPaymentTarifDelete');
    }
}