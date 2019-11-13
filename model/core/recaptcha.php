<?php
/**
 * Created by PhpStorm.
 * User: Egor
 * Date: 11.10.2019
 * Time: 10:07
 */
class Core_Recaptcha
{
    /**
     * Публичный HTML ключ
     *
     * @var string
     */
    private static $publicKey = '6LdowcEUAAAAAF6iRw05QXJ4WuAxv_ef4GbTewvY';

    /**
     * Секретный ключ
     *
     * @var string
     */
    private static $secretKey = '6LdowcEUAAAAAF2wgPzwwT1gAk2R2j9evzVeP3ck';

    /**
     * Расшифровка кодов ошибок ответа Google
     *
     * @var array
     */
    private static $errorCodesLang = [
        'missing-input-secret' =>   'Отсутствует секретный ключ',
        'invalid-input-secret' =>   'Указанный секретный ключ не существует или искажен.',
        'missing-input-response' => 'Значение reCAPTCHA не было указано либо указано неверно.',
        'invalid-input-response' => 'Значение передаваемого из формы хэша недопустимо или искажено.',
        'bad-request' =>            'Невозможно распознать переданный хэш.'
    ];


    private static $errors = [];



    /**
     * @return string
     */
    public static function getPublicKey()
    {
        return self::$publicKey;
    }


    /**
     * Получение текста ошибок
     *
     * @return string
     */
    public static function getErrorsStr()
    {
        $errorsMsg = '';
        foreach (self::$errors as $error) {
            $errorsMsg .= self::$errorCodesLang[$error] . PHP_EOL;
        }
        return $errorsMsg;
    }


    /**
     * Проверка введения Google reCAPTCHA
     *
     * @return bool
     */
    public static function checkRequest()
    {
        $recaptchaRequest = Core_Array::Request('g-recaptcha-response', null, PARAM_STRING);
        return self::isValid($recaptchaRequest);
    }


    /**
     * Отправка запроса в Google API и расшифровка ответа
     *
     * @param $request
     * @return bool
     */
    public static function isValid($request)
    {
        $postData = http_build_query(
            [
                'secret'    =>  self::$secretKey,
                'response'  =>  $request,
                'remoteip'  =>  Core_Array::Server('REMOTE_ADDR', '', PARAM_STRING)
            ]
        );

        $opts = [
            'http' => [
                'method'  => 'POST',
                'header'  => 'Content-type: application/x-www-form-urlencoded',
                'content' => $postData
            ]
        ];

        $context  = stream_context_create($opts);
        $response = file_get_contents(
            'https://www.google.com/recaptcha/api/siteverify',
            FALSE,
            $context
        );
        $response = json_decode($response , true);

        if ($response !== null && $response['success'] == true) {
            return true;
        } else {
            self::$errors = $response['error-codes'];
            return false;
        }
    }

}