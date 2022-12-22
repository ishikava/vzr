<?php

namespace Vzr\Helpers;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Logger
{
    public static function put($text, $needMail = true, $file = false, $str = false)
    {
        $backtrace = debug_backtrace();

        $traceStart = $backtrace[0];
        if (!$file) {
            $file = str_replace($_SERVER['DOCUMENT_ROOT'], '', $traceStart['file']);
        }
        if (!$str) {
            $str = $traceStart['line'];
        }


        $fileName = $_SERVER['DOCUMENT_ROOT'] . '/../var/log/' .  'log_' . date('m_Y') . '.log';

        if (!file_exists($fileName)) {
            if (!is_dir($fileName) && !mkdir(dirname($fileName), 0775, true)) {
                touch($fileName);
            }
        }

        $logRow = 'Message: ' . $text . "\r\n";
        $logRow .= 'File: ' . $file . ':' . $str . "\r\n";
        $logRow .= 'Date: ' . date('d.m.Y H:i:s') . "\r\n";
        $logRow .= 'Host: ' . $_SERVER["HTTP_HOST"] . "\r\n";

        file_put_contents($fileName, $logRow, FILE_APPEND);

        if ($needMail) {
            self::sendMail($logRow);
        }
    }

    private static function sendMail($message)
    {
        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->isSMTP();
            $mail->Host = '10.128.0.44';
            $mail->Port = 25;
            $mail->CharSet = 'UTF-8';
            $mail->SMTPSecure = false;
            $mail->SMTPAutoTLS = false;
            //Recipients
            $mail->setFrom('robot@Vzr.ru', 'Mailer');
            $mail->addAddress('KhasanovMR@Vzr.ru');
            $mail->addAddress('KochikDN@Vzr.ru');
            $mail->addAddress('SubbotinIV@Vzr.ru');
            $mail->addAddress('RyzhovMI@Vzr.ru');
            $mail->addAddress('LoktionovDS@Vzr.ru');
            $mail->addAddress('MukhutdinovAA@Vzr.ru');
            $mail->Subject = 'Ошибка на сайте vzrbonus.Vzr.ru';
            $mail->Body = $message;
            $mail->send();
            $mail->ClearAddresses();
            $mail->ClearAttachments();
        } catch (Exception $e) {
            self::put("Ошибка отправки заявки: {$mail->ErrorInfo}", false);
        }
    }
}
