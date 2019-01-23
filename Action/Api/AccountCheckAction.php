<?php
/**
 * Created by PhpStorm.
 * User: marius
 * Date: 19.1.21
 * Time: 15.53
 */

namespace PlumTreeSystems\SecureTrading\Action\Api;

use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use PlumTreeSystems\SecureTrading\Api;
use PlumTreeSystems\SecureTrading\Request\Api\AccountCheck;

class AccountCheckAction extends BaseApiAwareAction
{
    /**
     * @param mixed $request
     *
     * @throws \Payum\Core\Exception\RequestNotSupportedException if the action dose not support the request.
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $model = ArrayObject::ensureArrayObject($request->getModel());

        $response = $this->api->noChargeAuthorizeRequest($model->toUnsafeArrayWithoutLocal());
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
            $request instanceof AccountCheck &&
            $request->getModel() instanceof \ArrayAccess
            ;
    }
}