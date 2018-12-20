<?php
/**
 * Created by PhpStorm.
 * User: marius
 * Date: 18.12.20
 * Time: 15.38
 */

namespace Connector;


use PlumTreeSystems\SecureTrading\Model\ApiConnectorInterface;

class TestConnector implements ApiConnectorInterface
{

    public function process(array $fields): array
    {
        return ['responses' => [['errorcode' => '0']]];
    }

    public function getScriptImportUrl(): string
    {
        return '';
    }

    public function getScript(): string
    {
        return '';
    }
}