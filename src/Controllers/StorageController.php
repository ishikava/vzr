<?php

namespace Vzr\Controllers;

use Vzr\Services\NsService;
use Twig\Loader\FilesystemLoader;

class StorageController
{
    public static function setPrimary($type, $key, $params) {
        unset($_SESSION['vzr-data']);
        $_COOKIE['sign'] = $key;
        self::set($type, $key, $params);
    }
    public static function set($type, $key, $params)
    {
        setcookie("sign", $key, time()+86400, "/");
        $_SESSION['vzr-data'][$type] = $params;
    }

    public static function get($type) {
        $key=$_COOKIE["sign"];
        return $_SESSION['vzr-data'][$type];
    }
}
