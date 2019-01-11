<?php
/**
 * Created by PhpStorm.
 * User: marius
 * Date: 19.1.11
 * Time: 17.11
 */

namespace PlumTreeSystems\SecureTrading\Action\Api;

use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\ApiAwareTrait;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\LogicException;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use PlumTreeSystems\SecureTrading\Api;
use PlumTreeSystems\SecureTrading\Request\Api\ObtainToken;
use PlumTreeSystems\SecureTrading\Request\Api\SaveCreditCard;

class SaveCreditCardAction implements ActionInterface, GatewayAwareInterface, ApiAwareInterface
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
        /** @var $request SaveCreditCard */
        RequestNotSupportedException::assertSupports($this, $request);

        $model = ArrayObject::ensureArrayObject($request->getModel());

        if (false == $model['cachetoken']) {
            $obtainToken = new ObtainToken($request->getToken());
            $obtainToken->setModel($model);

            $this->gateway->execute($obtainToken);
        }

        $response = $this->api->storeCreditCardRequest($model->toUnsafeArrayWithoutLocal());
        $unwrappedResponse = Api::unwrapResponse($response);
        $model->replace($unwrappedResponse);
    }

    /**
     * @param mixed $request
     *
     * @return boolean
     */
    public function supports($request)
    {
        return
            $request instanceof SaveCreditCard &&
            $request->getModel() instanceof \ArrayAccess
            ;
    }
}