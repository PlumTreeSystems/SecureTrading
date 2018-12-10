<?php
/**
 * Created by PhpStorm.
 * User: marius
 * Date: 18.12.7
 * Time: 13.44
 */

namespace PlumTreeSystems\SecureTrading\Factory;


class SecureTradingApiFactory extends ApiFactory
{

    public function createApi()
    {
        return \Securetrading\api($this->options->toUnsafeArrayWithoutLocal());
    }
}