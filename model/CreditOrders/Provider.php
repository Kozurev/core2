<?php


namespace Model\CreditOrders;


/**
 * Class Provider
 * @package Model\CreditOrders
 */
abstract class Provider implements CreditProviderInterface
{
    /**
     * @var CreditOrderModel|null
     */
    protected ?CreditOrderModel $order = null;

    /**
     * @param CreditOrderModel $order
     * @return $this
     */
    public function setOrder(CreditOrderModel $order): self
    {
        $this->order = $order;
        return $this;
    }

    /**
     * @return CreditOrderModel
     * @throws \Exception
     */
    public function getOrder(): CreditOrderModel
    {
        if (is_null($this->order)) {
            throw new \Exception('Order not found in credit provider: ' . get_called_class());
        }
        return $this->order;
    }
}