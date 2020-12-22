<?php


namespace Model\Checkout;

use Model\Checkout\Model;

/**
 * Interface I_Checkout
 * @package Model\Checkout
 */
interface I_Checkout
{
    /**
     * I_Checkout constructor.
     * @param \Model\Checkout\Model $model
     */
    public function __construct(Model $model);

    /**
     * @return \Model\Checkout\Model
     */
    public function getModel() : Model;
}