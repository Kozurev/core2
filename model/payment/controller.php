<?php
/**
 * Created by PhpStorm.
 * User: Egor
 * Date: 19.06.2019
 * Time: 23:21
 * Class Payment_Controller
 */
class Payment_Controller extends Controller
{
    /**
     * @var string|null
     */
    protected ?string $dateFrom = null;

    /**
     * @var string|null
     */
    protected ?string $dateTo = null;

    /**
     * @param int|null $id
     * @param bool $isWithComments
     * @return Payment|null
     */
    public static function factory(int $id = null, bool $isWithComments = true) : ?Payment
    {
        if (is_null($id) || $id === 0) {
            return (new Payment());
        }

        $payment = Payment::find($id);
        if (is_null($payment)) {
            return null;
        }

        if ($isWithComments === true) {
            $commentsProperty = Property_Controller::factoryByTag('payment_comment');
            $payment->comments = $commentsProperty->getPropertyValues($payment);
        }

        return $payment;
    }
}