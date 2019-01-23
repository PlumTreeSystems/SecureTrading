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

    public function chargeRequest(array $fields)
    {
        $fields = array_merge($fields, [
            'accounttypedescription' => 'ECOM',
            'requesttypedescriptions' => ['AUTH']
        ]);
        return $this->doRequest($this->transformTransactionReference($fields));
    }

    public function recurringRequest(array $fields)
    {
        $fields = array_merge($fields, [
            'accounttypedescription' => 'RECUR',
            'requesttypedescriptions' => ['AUTH']
        ]);
        return $this->doRequest($this->transformTransactionReference($fields));
    }

    public function storeCreditCardRequest(array $fields)
    {
        $fields = array_merge($fields, [
            'accounttypedescription' => 'CARDSTORE',
            'requesttypedescriptions' => ['STORE']
        ]);
        return $this->doRequest($fields);
    }

    public function noChargeAuthorizeRequest(array $fields)
    {
        $fields = array_merge($fields, [
            'accounttypedescription' => 'ECOM',
            'requesttypedescriptions' => ['ACCOUNTCHECK']
        ]);
        return $this->doRequest($fields);
    }

    private function transformTransactionReference(array $fields)
    {
        if (isset($fields['transactionreference']) && !(isset($fields['parenttransactionreference']))) {
            $fields['parenttransactionreference'] = $fields['transactionreference'];
            $fields['transactionreference'] = null;
        }
        return $fields;
    }

    /**
     * @param array $fields
     *
     * @return array
     */
    protected function doRequest(array $fields)
    {
        $fields = array_merge($fields, [
            'sitereference' => $this->options['site_reference']
        ]);
        $response = $this->api->process($fields);

        return $response;
    }

    public function getSiteReference()
    {
        return $this->options['site_reference'];
    }

    public function getStaticJsAssets($siteReference, $locale)
    {
        return [
            'importUrl' => $this->api->getScriptImportUrl(),
            'scriptContent' => $this->api->getScript($siteReference, $locale)
        ];
    }

    public static function unwrapResponse($response)
    {
        if ($response) {
            if (isset($response['responses']) &&
                is_array($response['responses']) &&
                sizeof($response['responses']) === 1
            ) {
                return $response['responses'][0];
            }
        }
        throw new LogicException('Invalid response');
    }
}
