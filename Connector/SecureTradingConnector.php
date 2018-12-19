<?php
/**
 * Created by PhpStorm.
 * User: marius
 * Date: 18.12.19
 * Time: 15.56
 */

namespace PlumTreeSystems\SecureTrading\Connector;


use PlumTreeSystems\SecureTrading\Model\ApiConnectorInterface;
use Securetrading\Stpp\JsonInterface\Api;

class SecureTradingConnector implements ApiConnectorInterface
{
    /**
     * @var Api
     */
    private $api;

    /**
     * SecureTradingConnector constructor.
     * @param Api $api
     */
    public function __construct(Api $api)
    {
        $this->api = $api;
    }


    public function process(array $fields): array
    {
        return $this->api->process($fields);
    }


    public function getScriptImportUrl(): string
    {
        return 'https://webservices.securetrading.net/js/st.js';
    }

    public function getScript(): string
    {
        return "new SecureTrading.Standard({
            sitereference: '{{ site_reference }}',
            locale: '{{ locale }}'
        });";
    }
}