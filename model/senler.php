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
     * Название метода получения списка подписчиков
     */
    const METHOD_SUBSCRIBERS_GET = 'subscribers/get';

    /**
     * Название метода добавления пользователя в группу подписки
     */
    const METHOD_SUBSCRIBER_ADD = 'subscribers/add';

    /**
     * Название метода удаления пользователя из группы подписки
     */
    const METHOD_SUBSCRIBER_DELETE = 'subscribers/del';

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
     *
     */
    const PARAM_VK_USER_ID = 'vk_user_id';

    /**
     *
     */
    const PARAM_SUBSCRIPTION_ID = 'subscription_id';

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
     * @return stdClass|null
     * @throws Exception
     */
    public function getSubscriptionById($id)
    {
        $subscription = $this->getSubscriptions([self::PARAM_VK_USER_ID => $id]);
        return $subscription[0] ?? null;
    }

    /**
     * @param array $params
     * @return array|null
     * @throws Exception
     */
    public function getSubscribers(array $params = []) : array
    {
        $params[self::PARAM_VERSION_API] =  self::DEFAULT_API_VERSION;
        $params[self::PARAM_VK_GROUP_ID] =  $this->vkGroupId;
        $params[self::PARAM_HASH] =         self::getHash($params, $this->secretCallbackKey);

        $response = Api::getRequest(self::API_HOST . self::METHOD_SUBSCRIBERS_GET, $params, Api::REQUEST_METHOD_POST);
        if ($response->success !== true) {
            throw new Exception($response->error_message);
        } else {
            return $response->items;
        }
    }

    /**
     * @param $id
     * @return stdClass|null
     * @throws Exception
     */
    public function getSubscriberById($id)
    {
        $subscriber = $this->getSubscribers([self::PARAM_VK_USER_ID => $id]);
        return $subscriber[0] ?? null;
    }

    /**
     * Добавление пользователя в группу подписки
     *
     * @param $vkUserId
     * @param $subscriptionId
     * @return mixed
     * @throws Exception
     */
    public function subscribe($vkUserId, $subscriptionId = 0)
    {
        $params[self::PARAM_VERSION_API] =  self::DEFAULT_API_VERSION;
        $params[self::PARAM_VK_GROUP_ID] =  $this->vkGroupId;
        $params[self::PARAM_VK_USER_ID] =   $vkUserId;
        $params[self::PARAM_SUBSCRIPTION_ID]=$subscriptionId;
        $params[self::PARAM_HASH] =         self::getHash($params, $this->secretCallbackKey);

        return Api::getRequest(self::API_HOST . self::METHOD_SUBSCRIBER_ADD, $params, Api::REQUEST_METHOD_POST);
    }

    /**
     * Удаление пользователя из группы подписки илии всех групп сразу
     *
     * @param $vkUserId
     * @param int $subscriptionId
     * @return mixed
     * @throws Exception
     */
    public function subscribeRemove($vkUserId, $subscriptionId = 0)
    {
        $params[self::PARAM_VERSION_API] =  self::DEFAULT_API_VERSION;
        $params[self::PARAM_VK_GROUP_ID] =  $this->vkGroupId;
        $params[self::PARAM_VK_USER_ID] =   $vkUserId;
        $params[self::PARAM_SUBSCRIPTION_ID]=$subscriptionId;
        $params[self::PARAM_HASH] =         self::getHash($params, $this->secretCallbackKey);

        return Api::getRequest(self::API_HOST . self::METHOD_SUBSCRIBER_DELETE, $params, Api::REQUEST_METHOD_POST);
    }

    /**
     * @param Lid $lid
     * @param Lid_Status|null $status
     * @throws Exception
     */
    public static function setLidGroup(Lid $lid, Lid_Status $status = null)
    {
        if (is_null($status)) {
            $status = $lid->getStatus();
        }

        $link = $lid->vk();

        if (empty($link) || empty(explode('vk.com/', $link))) {
            return;
        }

//        if (empty($link) || substr($link, 0, 14) != 'https://vk.com') {
//            return;
//        }

        try {
            $lidVkId = Vk_Group::getVkId($link);
        } catch(Exception $e) {
            return;
        }

        if ($lidVkId->type != 'user') {
            return;
        }

        $lidInstrument = Property_Controller::factoryByTag('instrument')->getValues($lid)[0];
        $groups = (new Vk_Group_Controller(User_Auth::current()))->getList();
        foreach ($groups as $group) {
            // Orm::Debug(true);
            $setting = (new Senler_Settings())->queryBuilder()
                ->where('vk_group_id', '=', $group->getId())
                ->where('lid_status_id', '=', $status->getId())
                ->open()
                    ->where('training_direction_id', '=', $lidInstrument->value())
                    ->orWhere('training_direction_id', '=', 0)
                ->close()
                ->where('area_id', '=', $lid->areaId())
                ->orderBy('training_direction_id', 'DESC')
                ->find();
//debug($setting, 1);
            if (!is_null($setting)) {
                $senler = new Senler($group);
                $senler->subscribeRemove($lidVkId->object_id);
                $senler->subscribe($lidVkId->object_id, $setting->senlerSubscriptionId());
                break;
            }
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