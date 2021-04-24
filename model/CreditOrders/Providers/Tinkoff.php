<?php


namespace Model\CreditOrders\Providers;


use Model\CreditOrders\CreditOrderModel;
use Model\CreditOrders\CreditProviderInterface;
use Model\CreditOrders\Provider;
use Tightenco\Collect\Support\Collection;

/**
 * Class Tinkoff
 * @package Model\CreditOrders\Providers
 */
class Tinkoff extends Provider
{
    /**
     * @param int $userId
     * @param int $tariffId
     * @return $this
     */
    public function createOrder(int $userId, int $tariffId): self
    {

    }

    /**
     * @param Collection $requestData
     * @return mixed|void
     */
    public function changeStatusWebhook(Collection $requestData)
    {

    }

    /**
     * @param int $status
     * @return $this
     */
    public function changeStatus(int $status): self
    {

    }
}