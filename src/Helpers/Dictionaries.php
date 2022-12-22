<?php

namespace Vzr\Helpers;

class Dictionaries
{
    public static function getCurrency($currencyId) {
        $currencyList = array(
            'RUB' => 'Российский рубль',
            'USD' => 'Доллар США',
            'EUR' => 'Евро',
            'NOK' => 'Норвежская крона'
        );
        return $currencyList[$currencyId];
    }
}
