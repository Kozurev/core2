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
    const PARAM_TOKEN = 'token';
    const PARAM_AMOUNT = 'amount';
    const PARAM_SUCCESS_URL = 'returnUrl';
    const PARAM_ERROR_URL = 'failUrl';
    const PARAM_DESCRIPTION = 'description';
    const PARAM_ORDER_NUMBER = 'orderNumber';
    const PARAM_JSON_PARAMS = 'jsonParams';

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
    private ?int $orderNumber;

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
    private ?int $userId;

    /**
     * Примечание к платежу
     *
     * @var string|null
     */
    private ?string $description;

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
     * @return Sberbank
     */
    public static function instance()
    {
        $token = Property_Controller::factoryByTag('payment_sberbank_token')->getValues(User_Auth::current()->getDirector())[0]->value();
        return new self($token);
    }

    /**
     * Sberbank constructor.
     * @param string $token
     */
    private function __construct(string $token)
    {
        $this->token = $token;
        $this->successUrl = mapping('deposit_success');
        $this->errorUrl = mapping('deposit_error');
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
     * @return mixed|null
     */
    public function registerOrder()
    {
        $params = [
            self::PARAM_TOKEN => $this->token,
            self::PARAM_AMOUNT => $this->amount,
            self::PARAM_ORDER_NUMBER => $this->orderNumber,
            self::PARAM_SUCCESS_URL => $this->successUrl,
            self::PARAM_ERROR_URL => $this->errorUrl,
        ];
        $params[self::PARAM_JSON_PARAMS] = json_encode($params);
        return Api::getRequest($this->getUrl(self::ACTION_REGISTER_ORDER), $params);
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