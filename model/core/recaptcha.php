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
     * @var Core_Recaptcha
     */
    private static $_instance;


    /**
     * Публичный HTML ключ
     *
     * @var string
     */
    private $publicKey;

    /**
     * Секретный ключ
     *
     * @var string
     */
    private $secretKey;


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


    private $errors = [];


    private function __construct(string $secretKey, string $publicKey)
    {
        global $CFG;
        $this->publicKey = $CFG->recaptcha->publicKey;
        $this->secretKey = $CFG->recaptcha->secretKey;
    }


    /**
     * @return Core_Recaptcha
     */
    public static function instance()
    {
        if (empty(self::$_instance)) {
            global $CFG;
            self::$_instance = new Core_Recaptcha($CFG->recaptcha->secretKey, $CFG->recaptcha->publicKey);
        }
        return self::$_instance;
    }


    /**
     * @return string
     */
    public function getPublicKey()
    {
        return $this->publicKey;
    }


    /**
     * Получение текста ошибок
     *
     * @return string
     */
    public function getErrorsStr()
    {
        $errorsMsg = '';
        foreach ($this->errors as $error) {
            $errorsMsg .= self::$errorCodesLang[$error] . PHP_EOL;
        }
        return $errorsMsg;
    }


    /**
     * Проверка введения Google reCAPTCHA
     *
     * @return bool
     */
    public function checkRequest()
    {
        $recaptchaRequest = Core_Array::Request('g-recaptcha-response', null, PARAM_STRING);
        return $this->isValid($recaptchaRequest);
    }


    /**
     * Отправка запроса в Google API и расшифровка ответа
     *
     * @param $request
     * @return bool
     */
    public function isValid($request)
    {
        $postData = http_build_query(
            [
                'secret'    =>  $this->secretKey,
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
            $this->errors = $response['error-codes'];
            return false;
        }
    }

}