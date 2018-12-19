<?php
/**
 * Created by PhpStorm.
 * User: marius
 * Date: 18.12.7
 * Time: 13.44
 */

namespace PlumTreeSystems\SecureTrading\Factory;


use PlumTreeSystems\SecureTrading\Connector\SecureTradingConnector;

class SecureTradingApiFactory extends ApiFactory
{

    public function createApi()
    {
        $innerApi = \Securetrading\api($this->options->toUnsafeArrayWithoutLocal());
        return new SecureTradingConnector($innerApi);
    }
}