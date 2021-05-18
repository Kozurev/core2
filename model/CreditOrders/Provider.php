<?php


namespace Model\CreditOrders;


use Model\User\User_Client;

/**
 * Class Provider
 * @package Model\CreditOrders
 */
abstract class Provider implements CreditProviderInterface
{
    /**
     * @var CreditOrderModel|null
     */
    private ?CreditOrderModel $order = null;

    /**
     * @var User_Client|null
     */
    private ?User_Client $user = null;

    /**
     * @var \Payment_Tariff|null
     */
    private ?\Payment_Tariff $tariff = null;

    /**
     * @var string|null
     */
    private ?string $returnUrl = null;

    /**
     * @var string|null
     */
    private ?string $successUrl = null;

    /**
     * @var string|null
     */
    private ?string $failUrl = null;

    /**
     * @var bool
     */
    protected bool $testMode = true;

    /**
     * Provider constructor.
     * @param CreditOrderModel|null $order
     */
    public function __construct(?CreditOrderModel $order = null)
    {
        $this->order = $order;
        $this->returnUrl = mapping('credit_redirect');
        $this->successUrl = mapping('credit_redirect_success');
        $this->failUrl = mapping('credit_redirect_fail');
    }

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
     * @return CreditOrderModel|null
     * @throws \Exception
     */
    public function getOrder(): ?CreditOrderModel
    {
        return $this->order;
    }

    /**
     * @param User_Client $user
     * @return $this
     */
    public function setUser(User_Client $user): self
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return User_Client|null
     */
    public function getUser(): ?User_Client
    {
        return $this->user;
    }

    /**
     * @param \Payment_Tariff $tariff
     * @return $this
     */
    public function setTariff(\Payment_Tariff $tariff): self
    {
        $this->tariff = $tariff;
        return $this;
    }

    /**
     * @return \Payment_Tariff|null
     */
    public function getTariff(): ?\Payment_Tariff
    {
        return $this->tariff;
    }

    /**
     *
     */
    public function enableTestMode(): void
    {
        $this->testMode = true;
    }

    /**
     *
     */
    public function disableTestMode(): void
    {
        $this->testMode = false;
    }

    /**
     * @return bool
     */
    public function isTestMode(): bool
    {
        return $this->testMode;
    }

    /**
     * @return \stdClass
     * @throws \Exception
     */
    public function createUserParam(): \stdClass
    {
        if (is_null($this->getUser())) {
            throw new \Exception('Невозможно сгруппировать параметр "values" для создания заявки, так как отсутствует пользователь');
        }

        $user = new \stdClass();
        $user->contact = new \stdClass();
        $user->contact->fio = new \stdClass();
        $user->contact->fio->lastName = $this->getUser()->surname();
        $user->contact->fio->firstName = $this->getUser()->name();
        if (!empty($this->getUser()->patronymic())) {
            $user->contact->fio->middleName = $this->getUser()->patronymic();
        }
        if (!empty($this->getUser()->phoneNumber())) {
            $phone = preg_replace(['/\+7/', '/(^8)/', '/(^7)/'], ['', '', ''], $this->getUser()->phoneNumber());
            if (strlen($phone) === 10) {
                $user->contact->mobilePhone = $phone;
            }
        }

        return $user;
    }

    /**
     * @return \stdClass[]
     * @throws \Exception
     */
    protected function createItemsListParam(): array
    {
        if (is_null($this->getTariff())) {
            throw new \Exception('Невозможно сгруппировать параметр "items" для создания заявки, так как отсутствует тариф');
        }

        $item = new \stdClass();
        $item->quantity = 1;
        $item->name = $this->getTariff()->title();
        $item->price = $this->getTariff()->price();
        return [$item];
    }

    /**
     * @return string
     */
    public function getReturnUrl(): string
    {
        return $this->returnUrl;
    }

    /**
     * @return string
     */
    public function getSuccessUrl(): string
    {
        return $this->successUrl;
    }

    /**
     * @return string
     */
    public function getFailUrl(): string
    {
        return $this->failUrl;
    }
}