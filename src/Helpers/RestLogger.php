<?php

namespace Vzr\Helpers;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class RestLogger
{
    public static function put($text, $file = false, $str = false)
    {
        $backtrace = debug_backtrace();

        $traceStart = $backtrace[0];
        if (!$file) {
            $file = str_replace($_SERVER['DOCUMENT_ROOT'], '', $traceStart['file']);
        }
        if (!$str) {
            $str = $traceStart['line'];
        }


        $fileName = $_SERVER['DOCUMENT_ROOT'] . '/../var/log/' .  'restlog_' . date('m_Y') . '.log';

        if (!file_exists($fileName)) {
            if (!is_dir($fileName) && !mkdir(dirname($fileName), 0775, true)) {
                touch($fileName);
            }
        }

        $logRow = 'Message: ' . $text . "\r\n";
        $logRow .= 'File: ' . $file . ':' . $str . "\r\n";
        $logRow .= 'Date: ' . date('d.m.Y H:i:s') . "\r\n";

        file_put_contents($fileName, $logRow, FILE_APPEND);
    }
}
