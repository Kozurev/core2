<?php

/**
 * Class User_Balance
 */
class User_Balance extends User_Balance_Model
{
    /**
     * @param int $userId
     * @return static|null
     */
    public static function find(int $userId): ?self
    {
        return self::query()->where('user_id', '=', $userId)->find();
    }
}