<?php

/**
 * Created by PhpStorm.
 * User: leminhtoan
 * Date: 8/20/17
 * Time: 23:56
 */
class Log
{
    const ERROR = 1;
    const INFO = 2;

    /**
     * Log function
     * Implements writing to log files.

     * @param string $message The message you want to log.
     * @param string $path Log folder
     * @param integer $level
     * @return bool success of write.
     */
    public static function info($message, $level = self::INFO, $path = 'logs/all.log')
    {
        $levelText = 'INFO';
        if($level == self::ERROR){
            $levelText = 'ERROR';
        }

        $mask  = 0777;

        $exists = file_exists($path);
        $result = file_put_contents($path, date('Y/m/d H:i:s') . ", $levelText :" . $message . "\n", FILE_APPEND);
        static $selfError = false;

        if (!$selfError && !$exists && !chmod($path, (int)$mask)) {
            $selfError = true;
            trigger_error(vsprintf(
                'Could not apply permission mask "%s" on log file "%s"',
                [$mask, $path]
            ), E_USER_WARNING);
            $selfError = false;
        }

        return $result;
    }

//    /**
//     * Log function
//     * Implements writing to log files.
//
//     * @param string $message The message you want to log.
//     * @param string $logFolder Log folder
//     * @return bool success of write.
//     */
//    public static function error($message, $logFolder = 'logs')
//    {
//        $path = $logFolder . '/error.log';
//        $mask  = 0777;
//
//        $exists = file_exists($path);
//        $result = file_put_contents($path, date('Y/m/d H:i:s') . ', ERROR :' . $message . "\n", FILE_APPEND);
//        static $selfError = false;
//
//        if (!$selfError && !$exists && !chmod($path, (int)$mask)) {
//            $selfError = true;
//            trigger_error(vsprintf(
//                'Could not apply permission mask "%s" on log file "%s"',
//                [$mask, $path]
//            ), E_USER_WARNING);
//            $selfError = false;
//        }
//
//        return $result;
//    }

}