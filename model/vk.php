<?php

class VK extends Api
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
    const API_HOST = 'https://api.vk.com/method/';

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
        return self::getRequest(self::API_HOST . self::METHOD_RESOLVE_SCREEN_NAME, $requestParams);
    }

}