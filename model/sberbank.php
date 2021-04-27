<?php
/**
 * Created by PhpStorm.
 * User: Egor
 * Date: 27.06.2020
 * Time: 17:09
 */

use Model\Api;

class Sberbank
{
    const ACTION_REGISTER_ORDER = 'register.do';
    const ACTION_CHECK_STATUS = 'getOrderStatusExtended.do';

    const PARAM_TOKEN = 'token';
    const PARAM_AMOUNT = 'amount';
    const PARAM_SUCCESS_URL = 'returnUrl';
    const PARAM_ERROR_URL = 'failUrl';
    const PARAM_DESCRIPTION = 'description';
    const PARAM_ORDER_ID = 'orderId';
    const PARAM_ORDER_NUMBER = 'orderNumber';
    const PARAM_JSON_PARAMS = 'jsonParams';

    const ORDER_STATUS_PENDING = 0; //заказ зарегистрирован, но не оплачен
    const ORDER_STATUS_HOLD = 1; //предавторизованная сумма удержана (для двухстадийных платежей)
    const ORDER_STATUS_SUCCESS = 2; //проведена полная авторизация суммы заказа
    const ORDER_STATUS_CANCELED = 3; //авторизация отменена
    const ORDER_STATUS_REFUND = 4; //по транзакции была проведена операция возврата
    const ORDER_STATUS_AUTH_INIT = 5; //инициирована авторизация через сервер контроля доступа банка-эмитента
    const ORDER_STATUS_AUTH_CANCELED = 6; //авторизация отклонена

    /**
     * @var array|string[]
     */
    private static array $statuses = [
        self::ORDER_STATUS_PENDING      => 'Заказ зарегистрирован, но не оплачен',
        self::ORDER_STATUS_HOLD         => 'Предавторизованная сумма удержана (для двухстадийных платежей)',
        self::ORDER_STATUS_SUCCESS      => 'Проведена полная авторизация суммы заказа',
        self::ORDER_STATUS_CANCELED     => 'Авторизация отменена',
        self::ORDER_STATUS_REFUND       => 'По транзакции была проведена операция возврата',
        self::ORDER_STATUS_AUTH_INIT    => 'Инициирована авторизация через сервер контроля доступа банка-эмитента',
        self::ORDER_STATUS_AUTH_CANCELED=> 'Авторизация отклонена'
    ];

    /**
     * Авторизационный токен для платежного шлюза
     *
     * @var string
     */
    private string $token = '';

    /**
     * Режим работы платежного шлюза
     *
     * @var bool
     */
    private bool $isTestMode = false;

    /**
     * id платежа из таблицы "payment"
     *
     * @var int|null
     */
    private ?int $orderNumber = null;

    /**
     * Сумма платежа
     *
     * @var int|null
     */
    private ?int $amount;

    /**
     * id пользователя, который производит платеж
     *
     * @var int|null
     */
    private ?int $userId = null;

    /**
     * Примечание к платежу
     *
     * @var string|null
     */
    private ?string $description = null;

    /**
     * URL для редиректа после успешной оплаты
     *
     * @var string|null
     */
    private ?string $successUrl;

    /**
     * URL для редиректа, после неудачной оплаты
     *
     * @var string|null
     */
    private ?string $errorUrl;

    /**
     * API url для тестовых платежей
     *
     * @var string
     */
    private static string $testUrl = 'https://3dsec.sberbank.ru/payment/rest/';

    /**
     * API url для реальных платежей
     *
     * @var string
     */
    private static string $realUrl = 'https://securepayments.sberbank.ru/payment/rest';

    /**
     * Тестовый авторизационный токен
     *
     * @var string
     */
    private static string $testAuthToken = 'pqjg1i2mjl9qjdbmvg5rcok1n9';

    /**
     * @return Sberbank
     */
    public static function instance(): self
    {
        $token = Property_Controller::factoryByTag('payment_sberbank_token')
            ->getValues(User_Auth::current()->getDirector())[0]
            ->value();
        return new self($token);
    }

    /**
     * Sberbank constructor.
     * @param string $token
     */
    private function __construct(string $token)
    {
        $this->token = $token;
        $this->successUrl = mapping('deposit_success', ['token' => User_Auth::current()->authToken()]);
        $this->errorUrl = mapping('deposit_error', ['token' => User_Auth::current()->authToken()]);
    }

    /**
     * @return bool
     */
    public function isTestMode() : bool
    {
        return $this->isTestMode;
    }

    /**
     * @param bool $mode
     */
    public function setTestMode(bool $mode) : void
    {
        $this->isTestMode = $mode;
    }

    /**
     * @param int $orderNumber
     */
    public function setOrderNumber(int $orderNumber)
    {
        $this->orderNumber = $orderNumber;
    }

    /**
     * @param int $amount
     */
    public function setAmount(int $amount)
    {
        $this->amount = $amount;
    }

    /**
     * @param int $userId
     */
    public function setUserId(int $userId)
    {
        $this->userId = $userId;
    }

    public function setDescription(string $description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return !$this->isTestMode() ? $this->token : self::$testAuthToken;
    }

    /**
     * @param int $status
     * @return string
     */
    public static function getStatusName(int $status): string
    {
        return self::$statuses[$status] ?? 'Неизвестный статус';
    }

    /**
     * @return mixed|null
     */
    public function registerOrder()
    {
        $params = [
            self::PARAM_TOKEN => $this->getToken(),
            self::PARAM_AMOUNT => $this->amount,
            self::PARAM_ORDER_NUMBER => $this->orderNumber,
            self::PARAM_SUCCESS_URL => $this->successUrl,
            self::PARAM_ERROR_URL => $this->errorUrl,
            self::PARAM_DESCRIPTION => strval($this->description)
        ];
        $params[self::PARAM_JSON_PARAMS] = json_encode($params);
        return Api::getRequest($this->getUrl(self::ACTION_REGISTER_ORDER), $params);
    }

    /**
     * @param Payment $payment
     * @throws Exception
     */
    public function checkStatus(Payment $payment)
    {
        if (empty($payment->merchantOrderId())) {
            throw new Exception('Отсутствует ID платежа в платежном шлюзе');
        }
        $params = [
            self::PARAM_TOKEN => $this->token,
            self::PARAM_ORDER_ID => $payment->merchantOrderId()
        ];
        $response = Api::getRequest($this->getUrl(self::ACTION_CHECK_STATUS), $params);

        if ($payment->isStatusPending()) {
            if (!empty($response->errorCode ?? null)) {
                $payment->setStatusError();
                $payment->appendComment($response->errorMessage);
            } else {
                if ($response->orderStatus == self::ORDER_STATUS_SUCCESS) {
                    $payment->setStatusSuccess();
                } elseif ($response->orderStatus == self::ORDER_STATUS_CANCELED) {
                    $payment->setStatusCanceled();
                }
                $payment->appendComment('Статус платежа: ' . self::getStatusName($response->orderStatus) . '. ' . $response->actionCodeDescription);
            }
        }
    }

    /**
     * @param string $action
     * @return string
     */
    public function getUrl(string $action) : string
    {
        if ($this->isTestMode()) {
            $url = self::$testUrl;
        } else {
            $url = self::$realUrl;
        }
        return $url . '/' . $action;
    }

}