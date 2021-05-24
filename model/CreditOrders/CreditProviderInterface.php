<?php


namespace Model\CreditOrders;


use Model\User\User_Client;
use Illuminate\Support\Collection;

/**
 * Interface CreditProviderInterface
 * @package Model\CreditOrders
 */
interface CreditProviderInterface
{
    /**
     * CreditProviderInterface constructor.
     * @param CreditOrderModel|null $order
     */
    public function __construct(?CreditOrderModel $order = null);

    /**
     * @param User_Client $user
     * @param \Payment_Tariff $tariff
     * @return $this
     */
    public function createOrder(User_Client $user, \Payment_Tariff $tariff): \stdClass;

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
     * @return CreditOrderModel|null
     */
    public function getOrder(): ?CreditOrderModel;
}