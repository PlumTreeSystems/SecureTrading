<?php
/**
 * Created by PhpStorm.
 * User: marius
 * Date: 18.12.20
 * Time: 15.38
 */

namespace PlumTreeSystems\SecureTrading\Connector;


use PlumTreeSystems\SecureTrading\Model\ApiConnectorInterface;

class TestConnector implements ApiConnectorInterface
{

    public function process(array $fields): array
    {
        return (['responses' => [['errorcode' => '0', 'requesttypedescription' => 'AUTH']]]);
    }

    public function getScriptImportUrl(): string
    {
        return 'https://code.jquery.com/jquery-3.3.1.min.js';
    }

    public function getScript($siteRef, $locale): string
    {
        return "
            window.location.href = window.location.href + '?cachetoken=test'
        ";
    }
}