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
     * @return $this|null
     */
    public function save($obj = null)
    {
        Core::notify([&$this], 'before.PaymentTarif.save');
        if (is_null(parent::save())) {
            return null;
        }
        Core::notify([&$this], 'after.PaymentTarif.save');
        return $this;
    }


    /**
     * @param null $obj
     * @return $this|void
     */
    public function delete($obj = null)
    {
        Core::notify([&$this], 'before.PaymentTarif.delete');
        parent::delete();
        Core::notify([&$this], 'after.PaymentTarif.delete');
    }
}