<?php
/**
 * Created by PhpStorm.
 * User: marius
 * Date: 18.11.21
 * Time: 19.47
 */

namespace PlumTreeSystems\SecureTrading\Action\Api;

use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\ApiAwareTrait;
use Payum\Core\Exception\LogicException;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Reply\HttpResponse;
use Payum\Core\Request\GetHttpRequest;
use Payum\Core\Request\RenderTemplate;
use Payum\Core\Security\SensitiveValue;
use PlumTreeSystems\SecureTrading\Api;
use PlumTreeSystems\SecureTrading\Request\Api\ObtainToken;

class ObtainTokenAction implements ActionInterface, GatewayAwareInterface, ApiAwareInterface
{
    use ApiAwareTrait;

    use GatewayAwareTrait;

    protected $templateName;

    /**
     * ObtainTokenAction constructor.
     * @param $templateName
     */
    public function __construct($templateName)
    {
        $this->templateName = $templateName;
        $this->apiClass = Api::class;
    }

    /**
     * @param mixed $request
     *
     * @throws \Payum\Core\Exception\RequestNotSupportedException if the action dose not support the request.
     */
    public function execute($request)
    {
        /** @var $request ObtainToken */
        RequestNotSupportedException::assertSupports($this, $request);

        $model = ArrayObject::ensureArrayObject($request->getModel());

        if ($model['cachetoken']) {
            throw new LogicException('The token has already been set.');
        }

        $getHttpRequest = new GetHttpRequest();
        $this->gateway->execute($getHttpRequest);
        if ($getHttpRequest->method == 'GET' &&
            isset($getHttpRequest->query['cachetoken']) &&
            strlen($getHttpRequest->query['cachetoken'])
        ) {
            $model['cachetoken'] = SensitiveValue::ensureSensitive($getHttpRequest->query['cachetoken']);

            return;
        }

        $locale = Api::LOCALE_EN;
        $siteReference = $this->api->getSiteReference();
        $scriptAssets = $this->api->getStaticJsAssets($siteReference, $locale);

        $this->gateway->execute($renderTemplate = new RenderTemplate($this->templateName, array_merge([
            'model' => $model,
            'site_reference' => $siteReference,
            'locale' => $locale, // TODO: change this default
            'actionUrl' => $request->getToken() ? $request->getToken()->getTargetUrl() : null,
        ], $scriptAssets)));

        throw new HttpResponse($renderTemplate->getResult());
    }

    /**
     * @param mixed $request
     *
     * @return boolean
     */
    public function supports($request)
    {
        return
            $request instanceof ObtainToken &&
            $request->getModel() instanceof \ArrayAccess
            ;
    }
}