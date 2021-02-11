<?php

/**
 * Class Log
 */
class Log
{
    const TYPE_CRON = 'cron';
    const TYPE_PUSH = 'push';
    const TYPE_SENLER = 'senler';
    const TYPE_ORM = 'orm';
    const TYPE_SMS = 'sms';
    const TYPE_CORE = 'core';
    const TYPE_MAIL = 'mail';

    protected static ?self $_instance = null;

    /**
     * @var string
     */
    protected static string $logDir;

    /**
     * @var bool
     */
    protected bool $emailNotificationEnabled = true;

    /**
     * @var string
     */
    protected static string $emailNotification = ADMIN_EMAIL;

    /**
     * Log constructor.
     */
    private function __construct()
    {
        self::$logDir = ROOT . '/log';
    }

    /**
     * @return Log
     */
    public static function instance(): Log
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * @param string $type
     * @param string $message
     */
    public function debug(string $type, string $message)
    {
        $this->makeLog('debug', $type, $message);
    }

    /**
     * @param string $type
     * @param string $message
     */
    public function error(string $type, string $message)
    {
        $this->makeLog('error', $type, $message);
    }

    /**
     * @return bool
     */
    public function isEmailNotificationsEnabled(): bool
    {
        return $this->emailNotificationEnabled;
    }

    /**
     *
     */
    public function emailNotificationsOn(): void
    {
        $this->emailNotificationEnabled = true;
    }

    /**
     *
     */
    public function emailNotificationsOff(): void
    {
        $this->emailNotificationEnabled = false;
    }

    /**
     * @param $logType
     * @param $logDirName
     * @param $logData
     */
    private function makeLog($logType, $logDirName, $logData)
    {
        $logDir = self::$logDir . '/' . $logDirName;
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777);
        }

        $logTypeDir = $logDir . '/' . $logType;
        if (!is_dir($logTypeDir)) {
            mkdir($logTypeDir, 0777);
        }

        $newFileStr = date('Y-m-d H:i:s') . ' ' . $logData;
        file_put_contents($logTypeDir . '/log.txt', $newFileStr . PHP_EOL, FILE_APPEND);

        if ($this->isEmailNotificationsEnabled() && $logType === 'error') {
            $this->sendNotification($logData);
        }
    }

    /**
     * @param string $message
     */
    protected function sendNotification(string $message)
    {
        try {
            $mail = \Model\Mail::factory();
            $mail->addAddress(self::$emailNotification);
            $mail->Subject = 'Ошибка в musicmetod.ru';
            $mail->msgHTML($message);
            $mail->send();
        } catch (\Throwable $throwable) {
            $notificationEnabled = $this->emailNotificationEnabled;
            $this->emailNotificationsOff();
            $this->error(self::TYPE_MAIL, $throwable->getMessage());
            $this->emailNotificationEnabled = $notificationEnabled;
        }
    }

}