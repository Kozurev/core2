<?php

class VK
{
    /**
     * Название параметра, отвечающего за указание версии API
     */
    const PARAM_VERSION = 'v';

    /**
     * Название параметра, отвечающего за указание секретного ключа
     */
    const PARAM_ACCESS_TOKEN = 'access_token';

    /**
     * Используемая версия API по умолчанию
     */
    const DEFAULT_API_VERSION = '5.5';

    /**
     * Метод для преобразования ссылки в id страницы
     */
    const METHOD_RESOLVE_SCREEN_NAME = 'utils.resolveScreenName';

    /**
     * Ссылка на страницу API Вконтакте
     */
    const VK_API_HOST = 'https://api.vk.com/method';

    /**
     * Тип запроса к API вконтакте
     */
    const REQUEST_METHOD_GET = 'get';

    /**
     * Тип запроса к API вконтакте
     */
    const REQUEST_METHOD_POST = 'post';

    /**
     * Секретный ключ
     *
     * @var string
     */
    private $accessToken;

    /**
     * Используемая версия API
     *
     * @var string
     */
    private $apiVersion;

    /**
     * Список поддерживаемых методов
     *
     * @var array
     */
    private static $methods = [
        self::METHOD_RESOLVE_SCREEN_NAME
    ];



    /**
     * VK constructor.
     * @param string $token
     * @param string|null $apiVersion
     */
    public function __construct(string $token, string $apiVersion = null)
    {
        $this->accessToken = $token;
        $this->apiVersion = is_null($apiVersion)
            ?   self::DEFAULT_API_VERSION
            :   $apiVersion;
    }

    /**
     * @param $link
     * @return mixed
     * @throws Exception
     */
    public function resolveScreenName($link)
    {
        $requestParams = array(
            'screen_name' => $link,
            self::PARAM_VERSION => $this->apiVersion,
            self::PARAM_ACCESS_TOKEN => $this->accessToken
        );
        return self::getRequest(self::METHOD_RESOLVE_SCREEN_NAME, $requestParams);
    }

    /**
     * @param string $method
     * @param array $params
     * @param string $requestMethod
     * @return mixed
     * @throws Exception
     */
    private static function getRequest(string $method, array $params = [], string $requestMethod = self::REQUEST_METHOD_GET)
    {
        $queryParams = http_build_query($params);
        $apiUrl = self::VK_API_HOST . '/' . $method;

        if ($requestMethod === self::REQUEST_METHOD_GET) {
            return json_decode(file_get_contents($apiUrl .'?' . $queryParams));
        } elseif ($requestMethod === self::REQUEST_METHOD_POST) {
            return json_decode(file_get_contents($apiUrl, false, stream_context_create([
                'http' => [
                    'method' => 'POST',
                    'header' => 'Content-Type: application/x-www-form-urlencoded',
                    'content' => $queryParams
                ]
            ])));
        } else {
            throw new Exception('VK->getRequest - неопознанный тип запроса: ' . $requestMethod);
        }
    }


}