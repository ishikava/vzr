<?php


namespace Vzr\Helpers;


use DateTime;

class StringHelper
{
    /**
     * Binary version of strlen.
     * @param $str
     * @return int
     */
    public static function getLength($str)
    {
        return function_exists('mb_strlen') ? mb_strlen($str, 'latin1') : strlen($str);
    }

    /**
     * Binary version of substr.
     * @param $str
     * @param $start
     * @return string
     */
    public static function getSubstring($str, $start)
    {
        if(function_exists('mb_substr'))
        {
            $length = (func_num_args() > 2? func_get_arg(2) : self::getLength($str));
            return mb_substr($str, $start, $length, 'latin1');
        }
        if(func_num_args() > 2)
        {
            return substr($str, $start, func_get_arg(2));
        }
        return substr($str, $start);
    }

    public static function normalizePhone($number, $minLength = 10)
    {
        $minLength = intval($minLength);
        if ($minLength <= 0 || strlen($number) < $minLength)
        {
            return false;
        }

        if (strlen($number) >= 10 && substr($number, 0, 2) == '+8')
        {
            $number = '00'.substr($number, 1);
        }

        $number = preg_replace("/[^0-9\#\*]/i", "", $number);
        if (strlen($number) >= 10)
        {
            if (substr($number, 0, 2) == '80' || substr($number, 0, 2) == '81' || substr($number, 0, 2) == '82')
            {
            }
            else if (substr($number, 0, 2) == '00')
            {
                $number = substr($number, 2);
            }
            else if (substr($number, 0, 3) == '011')
            {
                $number = substr($number, 3);
            }
            else if (substr($number, 0, 1) == '8')
            {
                $number = '7'.substr($number, 1);
            }
            else if (substr($number, 0, 1) == '0')
            {
                $number = substr($number, 1);
            }
        }

        return $number;
    }

    public static function convertDate($date) {
        $dateTime = DateTime::createFromFormat('d.m.Y', $date);
        return $dateTime->format('Y-m-d');
    }
    public static function convertDateFromWS($date) {
        $dateArray=explode("T", $date);
        $dateTime = DateTime::createFromFormat('Y-m-d', $dateArray[0]);
        return $dateTime->format('d.m.Y');
    }

}
