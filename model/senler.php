<?php

class Senler extends Api
{
    /**
     * URL для API Senler-а
     */
    const API_HOST = 'https://senler.ru/api/';

    /**
     * Название метода полуучения списка групп в сенлере
     */
    const METHOD_SUBSCRIPTIONS_GET = 'subscriptions/get';

    /**
     * Обязательный параметр идентификатора группы ВК
     */
    const PARAM_VK_GROUP_ID = 'vk_group_id';

    /**
     * Версия API сенлера
     */
    const PARAM_VERSION_API = 'v';

    /**
     * Обязательный параметр из соли параметров и секретного ключа Callback API
     */
    const PARAM_HASH = 'hash';

    /**
     * Стандартно используемая версия API
     */
    const DEFAULT_API_VERSION = '1.0';

    /**
     * Идентификатор группы ВК, с которой интегрируется сенлер
     *
     * @var string
     */
    private $vkGroupId;

    /**
     * Секретный ключ, полученный в разделе настроек Callback API сообщества
     *
     * @var string
     */
    private $secretCallbackKey;


    /**
     * Список возможных ошибок
     *
     * @var array
     */
    private static $errors = [
        'empty_required_param_vk_id' =>                 'У группы не задано значение идентификатора вконтакте',
        'empty_required_param_secret_callback_key' =>   'У группы не задано значение секретного ключа из раздела Callback API'
    ];


    /**
     * Senler constructor.
     * @param Vk_Group $group
     * @throws Exception
     */
    public function __construct(Vk_Group $group)
    {
        if (self::isValidGroup($group)) {
            $this->vkGroupId = $group->vkId();
            $this->secretCallbackKey = $group->secretCallbackKey();
        }
    }

    /**
     * @param Vk_Group $group
     * @return bool
     * @throws Exception
     */
    public static function isValidGroup(Vk_Group $group) : bool
    {
        if (empty($group->vkId())) {
            throw new Exception(self::$errors['empty_required_param_vk_id']);
        }
        if (empty($group->secretCallbackKey())) {
            throw new Exception(self::$errors['empty_required_param_vk_id']);
        }
        return true;
    }

    /**
     * Формирование списка групп подписок в сенлере для определенного сообщества
     *
     * @param array $params
     * @return array
     * @throws Exception
     */
    public function getSubscriptions(array $params = []) : array
    {
        $params[self::PARAM_VERSION_API] =  self::DEFAULT_API_VERSION;
        $params[self::PARAM_VK_GROUP_ID] =  $this->vkGroupId;
        $params[self::PARAM_HASH] =         self::getHash($params, $this->secretCallbackKey);

        $response = Api::getRequest(self::API_HOST . self::METHOD_SUBSCRIPTIONS_GET, $params, Api::REQUEST_METHOD_POST);
        if ($response->success !== true) {
            throw new Exception($response->error_message);
        } else {
            return $response->items;
        }
    }

    /**
     * @param $id
     * @return mixed
     * @throws Exception
     */
    public function getSubscriptionById($id)
    {
        $params[self::PARAM_VERSION_API] =  self::DEFAULT_API_VERSION;
        $params[self::PARAM_VK_GROUP_ID] =  $this->vkGroupId;
        $params['subscription_id'] =        [$id];
        $params[self::PARAM_HASH] =         self::getHash($params, $this->secretCallbackKey);

        $response = Api::getRequest(self::API_HOST . self::METHOD_SUBSCRIPTIONS_GET, $params, Api::REQUEST_METHOD_POST);
        if ($response->success !== true) {
            throw new Exception($response->error_message);
        } else {
            return $response->items[0] ?? null;
        }
    }

    /**
     * Эта функция хэширования взята из документации
     *
     * @link https://help.senler.ru/api/formirovanie-podpisi
     * @param $params
     * @param $secret
     * @return string
     */
    private static function getHash(array $params, string $secret) : string
    {
        $values = '';
        foreach ($params as $value) {
            $values .= (is_array($value) ? implode("", $value) : $value);
        }
        return md5($values . $secret);
    }

}