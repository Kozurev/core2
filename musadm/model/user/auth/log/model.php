<?php
/**
 * Created by PhpStorm.
 * User: Egor
 * Date: 27.10.2019
 * Time: 4:03
 */
class User_Auth_Log_Model extends Core_Entity
{
    const DEVICE_PC = 1;
    const DEVICE_TABLET = 2;
    const DEVICE_MOBILE = 3;

    const SYSTEM_ANDROID = 1;
    const SYSTEM_IOS = 2;


    /**
     * id пользователя
     *
     * @var int
     */
    protected $user_id = 0;


    /**
     * дата и время последней авторизации формата "Y-m-d H:i:s"
     *
     * @var string
     */
    protected $datetime = '';


    /**
     * id устройства
     *
     * @var int
     */
    protected $device_id = 0;


    /**
     * id типа операционной системы (андроид/IOS)
     *
     * @var int
     */
    protected $system_id = 0;


    /**
     * ip адрес устройства
     *
     * @var string
     */
    protected $ip = '';


    /**
     * @param int|null $userId
     * @return $this|int
     */
    public function userId(int $userId = null)
    {
        if (is_null($userId)) {
            return intval($this->user_id);
        } else {
            $this->user_id = $userId;
            return $this;
        }
    }


    /**
     * @param string|null $datetime
     * @return $this|string
     */
    public function datetime(string $datetime = null)
    {
        if (is_null($datetime)) {
            return $this->datetime;
        } else {
            $this->datetime = $datetime;
            return $this;
        }
    }


    /**
     * @param int|null $deviceId
     * @return $this|int
     */
    public function deviceId(int $deviceId = null)
    {
        if (is_null($deviceId)) {
            return intval($this->device_id);
        } else {
            $this->device_id = $deviceId;
            return $this;
        }
    }


    /**
     * @param int|null $systemId
     * @return $this|int
     */
    public function systemId(int $systemId = null)
    {
        if (is_null($systemId)) {
            return intval($this->system_id);
        } else {
            $this->system_id = $systemId;
            return $this;
        }
    }


    /**
     * @param string|null $ip
     * @return $this|string
     */
    public function ip(string $ip = null)
    {
        if (is_null($ip)) {
            return $this->ip;
        } else {
            $this->ip = $ip;
            return $this;
        }
    }

}