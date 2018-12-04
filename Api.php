<?php
namespace PlumTreeSystems\SecureTrading;

use Http\Message\MessageFactory;
use Payum\Core\Exception\Http\HttpException;
use Payum\Core\HttpClientInterface;
use Payum\Core\Bridge\Spl\ArrayObject;

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
     * @var HttpClientInterface
     */
    protected $client;

    /**
     * @var MessageFactory
     */
    protected $messageFactory;

    /**
     * @var array
     */
    protected $options = [
        'username' => null,
        'password' => null,
        'site_reference' => null
    ];

    protected $api = null;

    /**
     * @param array               $options
     * @param HttpClientInterface $client
     * @param MessageFactory      $messageFactory
     *
     * @throws \Payum\Core\Exception\InvalidArgumentException if an option is invalid
     */
    public function __construct(array $options, HttpClientInterface $client, MessageFactory $messageFactory)
    {
        $options = ArrayObject::ensureArrayObject($options);
        $options->defaults($this->options);
        $options->validateNotEmpty([
            'username',
            'password',
            'site_reference'
        ]);

        $this->api = \Securetrading\api([
            $options->toUnsafeArrayWithoutLocal()
        ]);

        $this->options = $options;
        $this->client = $client;
        $this->messageFactory = $messageFactory;
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

        return $response;
    }

    public function getSiteReference()
    {
        return $this->options['site_reference'];
    }
}
