<?php
namespace PlumTreeSystems\SecureTrading;

use Payum\Core\Exception\LogicException;
use Payum\Core\Bridge\Spl\ArrayObject;
use PlumTreeSystems\SecureTrading\Model\ApiConnectorInterface;

class Api
{
    const LOCALE_EN = 'en_gb';
    const LOCALE_FR = 'fr_fr';
    const LOCALE_DE = 'de_de';

    const REQUEST_ACCOUNT = 'ACCOUNTCHECK';
    const REQUEST_AUTH = 'AUTH';
    const REQUEST_REFUND = 'REFUND';
    const REQUEST_THREEDQUERY = 'THREEDQUERY';
    const REQUEST_SUBSCRIPTION = 'SUBSCRIPTION';

    /**
     * @var array
     */
    protected $options = [
        'site_reference' => null
    ];

    /** @var ApiConnectorInterface */
    protected $api = null;

    /**
     * @param array               $options
     *
     * @throws \Payum\Core\Exception\InvalidArgumentException if an option is invalid
     */
    public function __construct($options, $api)
    {
        $options = ArrayObject::ensureArrayObject($options);
        $options->defaults($this->options);
        $options->validateNotEmpty([
            'site_reference'
        ]);

        if (strpos($options['site_reference'], 'test') === false) {
            throw new LogicException('Only test site reference are supported for now');
        }

        $this->api = $api;
        $this->options = $options;
    }

    public function simpleChargeRequest(array $fields)
    {
        $fields = array_merge($fields, [
            'accounttypedescription' => 'ECOM',
            'requesttypedescriptions' => ['AUTH'],
            'sitereference' => $this->options['site_reference']
        ]);
        return $this->doRequest($fields);
    }

    /**
     * @param array $fields
     *
     * @return array
     */
    protected function doRequest(array $fields)
    {
        $response = $this->api->process($fields);

        return $response->toArray();
    }

    public function getSiteReference()
    {
        return $this->options['site_reference'];
    }

    public function getStaticJsAssets()
    {
        return [
            'importUrl' => $this->api->getScriptImportUrl(),
            'scriptContent' => $this->api->getScript()
        ];
    }
}
