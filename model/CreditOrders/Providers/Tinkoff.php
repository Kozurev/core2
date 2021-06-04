<?php


namespace Model\CreditOrders\Providers;


use Model\CreditOrders\CreditOrderModel;
use Model\CreditOrders\Provider;
use Model\User\User_Client;
use Illuminate\Support\Collection;

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
    const PARAM_SHOWCASE_ID = 'showcaseId';
    const PARAM_AMOUNT = 'sum';
    const PARAM_ITEMS = 'items';
    const PARAM_ORDER_ID = 'orderNumber';
    const PARAM_SUCCESS_URL = 'successURL';
    const PARAM_FAIL_URL = 'failURL';
    const PARAM_RETURN_URL = 'returnURL';
    const PARAM_USER = 'values';
    const PARAM_PROMO_CODE = 'promoCode';

    const STATUS_APPROVED = 'approved';
    const STATUS_SIGNED = 'signed';
    const STATUS_REJECTED = 'rejected';
    const STATUS_CANCELED = 'canceled';

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

    /**
     * Идентификатор витрины(сайта) в системе провайдера
     *
     * @var string
     */
    private string $showcaseId;

    /**
     * Tinkoff constructor.
     * @param CreditOrderModel|null $order
     */
    public function __construct(?CreditOrderModel $order = null)
    {
        global $CFG;
        $this->shopId = $CFG->credits->tinkoff->shop_id;
        $this->showcaseId = $CFG->credits->tinkoff->showcase_id;
        parent::__construct($order);
    }

    /**
     * @param User_Client $user
     * @param \Payment_Tariff $tariff
     * @return \stdClass
     * @throws \Exception
     */
    public function createOrder(User_Client $user, \Payment_Tariff $tariff): \stdClass
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
            self::PARAM_SHOWCASE_ID => $this->showcaseId,
            self::PARAM_AMOUNT => $order->amount(),
            self::PARAM_ORDER_ID => $order->getId(),
            self::PARAM_ITEMS => $this->createItemsListParam(),
            self::PARAM_USER => $this->createUserParam(),
            self::PARAM_RETURN_URL => $this->getReturnUrl(),
            self::PARAM_SUCCESS_URL => $this->getSuccessUrl(),
            self::PARAM_FAIL_URL => $this->getFailUrl(),
            self::PARAM_PROMO_CODE => 'installment_0_0_6_6'
        ];

        $ch = curl_init($this->createUrl(self::ACTION_CREATE));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen(json_encode($params)),
            'Accept: application/json'
        ]);
        $response = json_decode(curl_exec($ch));
        curl_close($ch);

        if (is_array($response->errors ?? null) && count($response->errors) > 0) {
            throw new \Exception('Ошибка создания заявки: ' . implode('; ', $response->errors));
        }

        if (isset($response->id) && !empty($response->id)) {
            $this->getOrder()->providerId($response->id)->save();
        }

        return $response;
    }

    /**
     * @param Collection $requestData
     * @return void
     */
    public function changeStatusWebhook(Collection $requestData): void
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

        $id = $requestData->get('id');
        /** @var CreditOrderModel|null $creditOrder */
        $creditOrder = CreditOrderModel::query()
            ->where('provider_id', '=', $id)
            ->find();

        if (is_null($creditOrder)) {
            \Log::instance()->error('tinkoff', 'Ошибка вебхука - заявка не найдена; ' . $requestData->toJson());
            return;
        }

        $status = $requestData->get('status');
        if ($status == self::STATUS_APPROVED) { //заявка одобрена
            $creditOrder->status(CreditOrderModel::STATUS_APPROVED);
            $creditOrder->term(intval($requestData->get('term')));
            $creditOrder->monthlyPayment(floatval($requestData->get('monthly_payment')));
            $creditOrder->save();
        } elseif ($status == self::STATUS_REJECTED) { //По заявке отказ
            $creditOrder->status(CreditOrderModel::STATUS_REJECTED);
            $creditOrder->save();
        } elseif ($status == self::STATUS_CANCELED) { //Заявка отменена клиентом
            $creditOrder->status(CreditOrderModel::STATUS_CANCELED);
            $creditOrder->save();
        } elseif ($status == self::STATUS_SIGNED) { //Договор подписан
            $creditOrder->status(CreditOrderModel::STATUS_SIGNED);
            $creditOrder->save();

            $payment = (new \Payment())
                ->user($creditOrder->userId())
                ->status(\Payment::STATUS_SUCCESS)
                ->type(\Payment::TYPE_INCOME)
                ->description('Оформление рассрочки')
                ->value($creditOrder->amount())
                ->save();
            $payment->appendComment('Заявка № ' . $creditOrder->providerId());

            $client = User_Client::find($creditOrder->userId());
            $tariff = \Payment_Tariff::find($creditOrder->tariffId());
            if (is_null($client) || is_null($tariff)) {
                $payment->delete();
                if (is_null($client)) {
                    \Log::instance()->error('tinkoff', 'Клиент с id ' . $creditOrder->userId() . ' не найден. Заявка № ' . $creditOrder->providerId());
                }
                if (is_null($tariff)) {
                    \Log::instance()->error('tinkoff', 'Тариф с id ' . $creditOrder->tariffId() . ' не найден. Заявка № ' . $creditOrder->providerId());
                }
                return;
            }
            try {
                $client->buyTariff($tariff);
            } catch (\Throwable $throwable) {
                $payment->delete();
                \Log::instance()->error('tinkoff', $throwable->getMessage());
            }
        } else {
            \Log::instance()->error('tinkoff', 'неизвестный статус вебхука: ' . $requestData->toJson());
        }
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