<?php


namespace Model;

use Model\Checkout\InitPro;
use Model\Checkout\Model;

class Checkout
{
    const INIT_PRO = 1;

    /**
     * @var array|int[]
     */
    protected static array $checkouts = [
        self::INIT_PRO => InitPro::class
    ];

    public static function instance(Model $model)
    {

    }

}