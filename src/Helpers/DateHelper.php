<?php

namespace Vzr\Helpers;

class DateHelper
{
    public static function checkDate()
    {
        $dateTime = \DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d') . " 00:00:00");
        $timestamp = $dateTime->getTimestamp();
        if (time() > $timestamp && (time() - $timestamp) < 3600) {
            $dateTime = $dateTime->setTimestamp(time() - 86400);
            return $dateTime->format('d-m-Y');
        }
        return false;
    }
}
