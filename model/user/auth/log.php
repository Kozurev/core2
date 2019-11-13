<?php
/**
 * Created by PhpStorm.
 * User: Egor
 * Date: 27.10.2019
 * Time: 3:15
 */
class User_Auth_Log extends User_Auth_Log_Model
{
    /**
     * @param User $user
     * @return $this|null
     */
    public static function create(User $user)
    {
        Core::requireClass('User_Detect');

        $log = new User_Auth_Log;
        $log->userId($user->getId());
        $log->ip(Core_Array::Server('REMOTE_ADDR', ''));

        $detect = new User_Detect;
        if ($detect->isMobile()) {
            $log->deviceId(self::DEVICE_MOBILE);
        } elseif ($detect->isTablet()) {
            $log->deviceId(self::DEVICE_TABLET);
        } else {
            $log->deviceId(self::DEVICE_PC);
        }

        if ($detect->isAndroidOS()) {
            $log->systemId(self::SYSTEM_ANDROID);
        } elseif ($detect->isiOS()) {
            $log->systemId(self::SYSTEM_IOS);
        }

        return $log->save();
    }


    /**
     * @param User $user
     * @return User_Auth_Log|null
     */
    public static function getLast(User $user)
    {
        $lastLog = new User_Auth_Log;
        return $lastLog->queryBuilder()
            ->where('user_id', '=', $user->getId())
            ->orderBy('id', 'DESC')
            ->find();
    }


    /**
     * @param User $user
     * @param string $format
     * @return string
     */
    public static function getLastDate(User $user, string $format = 'd.m.y H:i') : string
    {
        $lastEntry = self::getLast($user);
        return !empty($lastEntry)
            ?   date($format, strtotime($lastEntry->datetime()))
            :   '';
    }


    /**
     * @return $this|null
     */
    public function save()
    {
        Core::notify([&$this], 'before.UserAuthLog.save');

        if (empty($this->datetime())) {
            $this->datetime(date('Y-m-d H:i:s'));
        }

        if (empty(parent::save())) {
            return null;
        }

        Core::notify([&$this], 'after.UserAuthLog.save');
        return $this;
    }

}