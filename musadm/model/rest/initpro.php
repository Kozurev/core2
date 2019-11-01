<?php
/**
 * Created by PhpStorm.
 * User: Egor
 * Date: 31.08.2019
 * Time: 16:02
 */
class Rest_Initpro
{
    const METHOD_POST = 'post';
    const METHOD_GET = 'get';

    private static $login = 'HOZfFAV';
    private static $pass = 'FMzxGBmib1NE7h3K';
    private static $groupCode = 'bkNTVIctES';
    private static $apiVersion = 'v1';
    private static $apiUrl = 'https://kassa.initpro.ru/lk/api';


    /**
     * Объект ошибки авторизации, если такая имеется
     *
     * @var array
     */
    public static $authError = [
        'error_id'  => null,
        'code'      => null,
        'text'      => null,
        'type'      => null
    ];


    /**
     * Авторизационный токен
     *
     * @var string
     */
    private static $authToken;


    /**
     * Адрес ответа регистрации чека
     *
     * @var string
     */
    private static $callbackUrl = 'http://musicmetod.ru/musadm/api/initpro?action=checkCallback';


    /**
     * Список названий всевозможных команд для API
     *
     * @var array
     */
    private static $actions = [
        'token' =>  'getToken',     //Получение токена
        'pay' =>    'sell'          //ФОрмирование чека на приход средств
    ];



    /**
     * @param string $method
     * @return bool
     */
    static function makeAuth($method = self::METHOD_POST)
    {
        $params = [];
        $params['login'] = self::$login;
        $params['pass'] = self::$pass;

        if ($method === self::METHOD_GET) {
            $url = self::makeUrl(null, self::$actions['token'], $params);
        } else {
            $url = self::makeUrl(null, self::$actions['token'], []);
        }

        if ($method === self::METHOD_POST) {
            $streamContext = stream_context_create([
                'http' => [
                    'method'        => 'POST',
                    'ignore_errors' => true,
                    'header'        => 'Content-type: application/json; charset=utf-8',
                    'content'       => json_encode($params)
                ]
            ]);
        } else {
            $streamContext = null;
        }

        $response = json_decode(@file_get_contents($url, false, $streamContext));

        if (empty($response)) {
            self::$authError['error_id'] =  0;
            self::$authError['code'] =      0;
            self::$authError['text'] =      'Неизвестная ошибка';
            self::$authError['type'] =      'System';
        } elseif (!empty($response->error)) {
            self::$authError = (array)$response->error;
        } else {
            self::$authToken = $response->token;
        }

        return !empty(self::$authToken);
    }


    /**
     * @param $payment
     * @return mixed
     */
    public static function sendCheck($payment)
    {
        $check = new stdClass();
        $check->external_id = strval($payment->id);
        $check->timestamp = date('d.m.y H:i:s');
        $check->service = new stdClass();
        $check->service->callback_url = self::$callbackUrl;
        $check->receipt = new stdClass();

        //$check->receipt->client = new stdClass();
        $check->receipt->client = $payment->client;

        $checkCompany = new stdClass();
        $checkCompany->email = 'musicmetod@mail.ru';
        $checkCompany->sno = 'patent';
        $checkCompany->inn = '311515761002';
        $checkCompany->payment_address = 'http://musicmetod.ru';
        $check->receipt->company = $checkCompany;

        $checkItem = new stdClass();
        $checkItem->name = $payment->description;
        $checkItem->price = $payment->sum;
        $checkItem->quantity = 1.0;
        $checkItem->sum = $payment->sum;
        $checkItem->payment_method = 'full_prepayment';
        $checkItem->payment_object = 'payment';
        $checkItem->vat = new stdClass();
        $checkItem->vat->type = 'none';
        $check->receipt->items = [$checkItem];

        $checkPayment = new stdClass();
        $checkPayment->type = 1;
        $checkPayment->sum = $payment->sum;
        $check->receipt->payments = [$checkPayment];

        $checkVat = new stdClass();
        $checkVat->type = 'none';
        $checkVat->sum = 0.0;
        $check->receipt->vats = [$checkVat];

        $check->receipt->total = $payment->sum;

        $url = self::makeUrl(self::$groupCode, self::$actions['pay'], ['token' => self::$authToken]);
        $streamContext = stream_context_create([
            'http' => [
                'method'        => 'POST',
                'ignore_errors' => true,
                'header'        => 'Content-type: application/json; charset=utf-8',
                'content'       => json_encode($check)
            ]
        ]);
        $response = json_decode(@file_get_contents($url, false, $streamContext));
        return json_encode($response);
    }


    /**
     * Формирование API URL касс инитпро
     *
     * @param string $action
     * @param array $data
     * @return string
     */
    static function makeUrl($groupsCode = null, string $action, $data = [])
    {
        $url = self::$apiUrl . '/' . self::$apiVersion;
        if (!is_null($groupsCode)) {
            $url .= '/' . $groupsCode;
        }
        $url .= '/' . $action;

        if (empty($data)) {
            return $url;
        } else {
            return $url . '?' . http_build_query($data);
        }
    }

}