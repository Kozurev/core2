<?php
/**
 * Created by PhpStorm.
 * User: Egor
 * Date: 19.06.2019
 * Time: 23:21
 * Class Payment_Controller
 */
class Payment_Controller
{
    /**
     * @param int|null $id
     * @param bool $isWithComments
     * @return Payment|null
     */
    public static function factory(int $id = null, bool $isWithComments = true)
    {
        if (is_null($id) || $id === 0) {
            return Core::factory('Payment');
        }

        $Payment = Core::factory('Payment', $id);
        if (is_null($Payment)) {
            return null;
        }

        if ($isWithComments === true) {
            $CommentsProperty = Core::factory('Property')->getByTagName('payment_comment');
            $Payment->comments = $CommentsProperty->getPropertyValues($Payment);
        }

        return $Payment;
    }


}