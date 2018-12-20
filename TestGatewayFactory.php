<?php
/**
 * Created by PhpStorm.
 * User: marius
 * Date: 18.12.20
 * Time: 15.40
 */

namespace PlumTreeSystems\SecureTrading;

use Connector\TestConnector;

class TestGatewayFactory extends SecureTradingGatewayFactory
{
    protected function createApi($config)
    {
        return new TestConnector();
    }

}