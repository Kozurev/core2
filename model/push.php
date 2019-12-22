<?php

use Kreait\Firebase;
use Kreait\Firebase\Exception\FirebaseException;
use Kreait\Firebase\Exception\MessagingException;
use Kreait\Firebase\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Factory;

class Push
{
    /**
     * @var Push
     */
    private static $_instance;


    /**
     * @var Factory
     */
    private $factory;


    /**
     * @var Messaging
     */
    private $messaging;


    /**
     * @var Messaging\Notification
     */
    private $notification;


    /**
     * @var string
     */
    private static $configJsonPath = ROOT . '/firebase.json';


    private function __construct() {}


    /**
     * @return Push
     */
    public static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new Push();
            self::$_instance->factory = (new Factory())->withServiceAccount(self::$configJsonPath);
            self::$_instance->messaging = self::$_instance->factory->createMessaging();
        }
        return self::$_instance;
    }


    /**
     * @param array $data
     * @return Push
     */
    public function notification(array $data)
    {
        if (!isset($data['sound'])) {
            $data['sound'] = 'default';
        }

        self::instance()->notification = Messaging\Notification::fromArray($data);
        return self::instance();
    }


    /**
     * @param $token
     * @return array|Messaging\MulticastSendReport
     * @throws FirebaseException
     * @throws MessagingException
     */
    public function send($token)
    {
        if (is_string($token)) {
            $message = CloudMessage::withTarget('token', $token)->withNotification($this->notification);
            return $this->messaging->send($message);
        } elseif (is_array($token)) {
            $message = CloudMessage::new()->withNotification($this->notification);
            return $this->messaging->sendMulticast($message, $token);
        } else {
            return null;
        }
    }
}