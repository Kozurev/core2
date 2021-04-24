<?php


namespace Model\CreditOrders;


use Tightenco\Collect\Support\Collection;

/**
 * Interface CreditProviderInterface
 * @package Model\CreditOrders
 */
interface CreditProviderInterface
{
    /**
     * @return static
     */
    public function createOrder(int $userId, int $tariffId): self;

    /**
     * @param Collection $requestData
     * @return mixed
     */
    public function changeStatusWebhook(Collection $requestData);

    /**
     * @param int $status
     * @return $this
     */
    public function changeStatus(int $status): self;

    /**
     * @return CreditOrderModel
     */
    public function getOrder(): CreditOrderModel;
}