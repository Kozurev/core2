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

    protected static ?self $_instance = null;

    /**
     * @var string
     */
    protected static string $logDir;

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
    public static function instance()
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
        self::makeLog('debug', $type, $message);
    }

    /**
     * @param string $type
     * @param string $message
     */
    public function error(string $type, string $message)
    {
        self::makeLog('error', $type, $message);
    }

    /**
     * @param $logType
     * @param $logDirName
     * @param $logData
     */
    private static function makeLog($logType, $logDirName, $logData)
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
    }

}