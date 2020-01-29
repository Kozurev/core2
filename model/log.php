<?php


class Log
{
    const TYPE_CRON = 'cron';
    const TYPE_PUSH = 'push';

    protected static $_instance;

    protected static $logDir;

    private function __construct()
    {
        self::$logDir = ROOT . '/log';
    }

    public static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }


    public function debug(string $type, string $message)
    {
        self::makeLog('debug', $type, $message);
    }


    public function error(string $type, string $message)
    {
        self::makeLog('error', $type, $message);
    }


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

        $logFileName = fopen($logTypeDir . '/' . date('Y_m_d_H:i:s') . '.txt', 'w');
        fwrite($logFileName, $logData);
        fclose($logFileName);
    }

}