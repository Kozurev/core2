<?php
/**
 * Created by PhpStorm.
 * User: Egor
 * Date: 27.06.2020
 * Time: 17:09
 */

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

    private $token = '';

    private $isTestMode = true;

    private $orderNumber;
    private $amount;
    private $userId;
    private $description;
    private $successUrl;
    private $errorUrl;

    private static $testUrl = 'https://3dsec.sberbank.ru/payment/rest/';
    private static $realUrl = 'https://securepayments.sberbank.ru/payment/rest';

    public static function instance()
    {
        $token = Property_Controller::factoryByTag('payment_sberbank_token')->getValues(User_Auth::current()->getDirector())[0]->value();
        return new self($token);
    }

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

    public function setOrderNumber(int $orderNumber)
    {
        $this->orderNumber = $orderNumber;
    }

    public function setAmount(int $amount)
    {
        $this->amount = $amount;
    }

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