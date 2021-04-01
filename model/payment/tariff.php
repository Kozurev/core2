<?php
/**
 * Класс реализующий методы для работы с тарифом
 *
 * @method static self|null find(int $id)
 *
 * @author BadWolf
 * @date 28.04.2018 16:07
 * @version 20190328
 * Class Payment_Tariff
 */
class Payment_Tariff extends Payment_Tariff_Model
{
    /**
     * @param null $obj
     * @return $this|null
     */
    public function save($obj = null): ?self
    {
        Core::notify([&$this], 'before.PaymentTariff.save');
        if (is_null(parent::save())) {
            return null;
        }
        Core::notify([&$this], 'after.PaymentTariff.save');
        return $this;
    }


    /**
     * @param null $obj
     * @return $this|void
     */
    public function delete($obj = null)
    {
        Core::notify([&$this], 'before.PaymentTariff.delete');
        parent::delete();
        Core::notify([&$this], 'after.PaymentTariff.delete');
    }
}