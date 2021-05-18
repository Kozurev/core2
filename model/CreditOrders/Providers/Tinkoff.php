<?php


namespace Model\CreditOrders\Providers;


use Model\CreditOrders\CreditOrderModel;
use Model\CreditOrders\Provider;
use Model\User\User_Client;
use Tightenco\Collect\Support\Collection;

/**
 * Class Tinkoff
 * @package Model\CreditOrders\Providers
 */
class Tinkoff extends Provider
{
    const ACTION_CREATE = 1;
    const ACTION_CANCEL = 2;
    const ACTION_COMMIT = 3;
    const ACTION_INFO = 4;

    const PARAM_SHOP_ID = 'shopId';
    const PARAM_AMOUNT = 'sum';
    const PARAM_ITEMS = 'items';
    const PARAM_ORDER_ID = 'orderNumber';
    const PARAM_SUCCESS_URL = 'successURL';
    const PARAM_FAIL_URL = 'failURL';
    const PARAM_RETURN_URL = 'returnURL';

    /**
     * @var string
     */
    protected static string $defaultUrl = 'https://forma.tinkoff.ru/api/partners/v2/orders/';

    /**
     * URL для различных действий.
     * Массив с индексом 0 - для тестового режима
     * Массив с индексом 1 - для боевого режима
     *
     * @var array
     */
    protected static array $actions = [
        [
            self::ACTION_CREATE => 'create',
            self::ACTION_CANCEL => '{orderNumber}/cancel',
            self::ACTION_COMMIT => '{orderNumber}/commit',
            self::ACTION_INFO => '{orderNumber}/info'
        ],
        [
            self::ACTION_CREATE => 'create-demo',
            self::ACTION_CANCEL => '{orderNumber}/cancel',
            self::ACTION_COMMIT => '{orderNumber}/commit',
            self::ACTION_INFO => '{orderNumber}/info'
        ]
    ];

    /**
     * ID магазина в системе провайдера
     *
     * @var string
     */
    private string $shopId;

//    /**
//     * Пароль для авторизации
//     *
//     * @var string
//     */
//    private string $password;

    /**
     * Tinkoff constructor.
     * @param CreditOrderModel|null $order
     */
    public function __construct(?CreditOrderModel $order = null)
    {
        global $CFG;
        $this->shopId = $CFG->credits->tinkoff->shop_id;
        // $this->password = $CFG->credits->tinkoff->password;
        parent::__construct($order);
    }

    /**
     * @param User_Client $user
     * @param \Payment_Tariff $tariff
     * @return $this
     * @throws \Exception
     */
    public function createOrder(User_Client $user, \Payment_Tariff $tariff): self
    {
        $this->setTariff($tariff);
        $this->setUser($user);

        $order = new CreditOrderModel();
        $order->userId($user->getId());
        $order->tariffId($tariff->getId());
        $order->amount($tariff->price());
        $order->provider(CreditOrderModel::PROVIDER_TINKOFF);
        $order->save();
        $this->setOrder($order);

        $params = [
            self::PARAM_SHOP_ID => $this->shopId,
            self::PARAM_AMOUNT => $order->amount(),
            self::PARAM_ORDER_ID => $order->getId(),
            self::PARAM_ITEMS => $this->createItemsList(),
            self::PARAM_RETURN_URL => $this->getReturnUrl(),
            self::PARAM_SUCCESS_URL => $this->getSuccessUrl(),
            self::PARAM_FAIL_URL => $this->getFailUrl()
        ];

        try {
            $response = file_get_contents($this->createUrl(self::ACTION_CREATE), false, stream_context_create([
                'http' => [
                    'method' => 'POST',
                    'header' => 'Content-Type: application/json',
                    'content' => json_encode($params)
                ]
            ]));
            dd($response);
        } catch (\Throwable $throwable) {
            dd($throwable->getMessage());
        }

        return $this;
    }

    /**
     * @param Collection $requestData
     * @return mixed|void
     */
    public function changeStatusWebhook(Collection $requestData)
    {
        \Log::instance()->debug('tinkoff', json_encode([
            'request' => $requestData->all(),
            'server' => [
                'HTTP_REFERER' => $_SERVER['HTTP_REFERER'] ?? null,
                'REMOTE_ADDR' => $_SERVER['REMOTE_ADDR'] ?? null,
                'REMOTE_HOST' => $_SERVER['REMOTE_HOST'] ?? null,
                'REMOTE_PORT' => $_SERVER['REMOTE_PORT'] ?? null
            ]
        ]));
    }

    /**
     * @param int $status
     * @return $this
     */
    public function changeStatus(int $status): self
    {

    }

    /**
     * @param int $action
     * @return string
     * @throws \Exception
     */
    public function createUrl(int $action): string
    {
        $url = self::$defaultUrl . (self::$actions[intval($this->isTestMode())][$action] ?? '');
        if (!is_null($this->getOrder())) {
            $url = preg_replace('/{orderNumber}/', $this->getOrder()->getId(), $url);
        }
        return $url;
    }
}