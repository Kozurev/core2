<?php


namespace Model\CreditOrders;


use Model\CreditOrders\Providers\Tinkoff;

/**
 * Class CreditServiceProvider
 * @package Model\CreditOrders
 */
class CreditServiceProvider
{
    /**
     * @var Provider
     */
    protected Provider $provider;

    /**
     * @var string
     */
    protected static string $defaultProvider = Tinkoff::class;

    /**
     * CreditServiceProvider constructor.
     * @param string|null $providerClass
     */
    public function __construct(?string $providerClass = null)
    {
        $this->provider = !is_null($providerClass)
            ?   new $providerClass
            :   new self::$defaultProvider;
    }

    /**
     * @return Provider
     */
    public function getProvider(): Provider
    {
        return $this->provider;
    }
}