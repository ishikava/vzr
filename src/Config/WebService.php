<?php

namespace Vzr\Config;

/**
 * Class WebService
 * @package Vzr\Config
 */
class WebService {

    /**
     * @return \string[][]
     */
    public static function getEnv(): array
    {
        if($_SERVER["SERVER_NAME"] != 'vzrbonus.Vzr.ru' && !defined('DEV_ENVIRONMENT')) {
            $env = 'dev';
        } else {
            $env = 'prod';
        }
        return self::getMap()[$env];
    }


    /**
     * @return \string[][][]
     */
    private static function getMap(): array
    {
        return array(
            'prod' => array(
                'contractInfo' => array(
                    'host' => 'https://b2b.Vzr.ru/cxf/AvisMobile?wsdl',
                    'user' => 'partner',
                    'password' => 'alfa'
                )
            ),
            'dev' => array(
                'contractInfo' => array(
                    'host' => 'https://b2b-test.Vzr.ru/cxf/AvisMobile?wsdl',
                    'user' => 'partner',
                    'password' => 'alfa'
                )
            )
        );
    }
}