<?php
namespace PlumTreeSystems\SecureTrading\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\ApiAwareTrait;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\LogicException;
use Payum\Core\Exception\UnsupportedApiException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Request\Capture;
use Payum\Core\Exception\RequestNotSupportedException;
use PlumTreeSystems\SecureTrading\Api;
use PlumTreeSystems\SecureTrading\Request\Api\AccountCheck;
use PlumTreeSystems\SecureTrading\Request\Api\ObtainToken;

class CaptureAction implements ActionInterface, GatewayAwareInterface, ApiAwareInterface
{
    use GatewayAwareTrait;

    use ApiAwareTrait;

    /**
     * CaptureAction constructor.
     */
    public function __construct()
    {
        $this->apiClass = Api::class;
    }


    /**
     * {@inheritDoc}
     *
     * @param Capture $request
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $model = ArrayObject::ensureArrayObject($request->getModel());

        if (isset($model['errorcode'])) {
            return;
        }

        if (false == $model['credentialsonfile'] || $model['credentialsonfile'] !== '2') {
            if (false == $model['cachetoken']) {
                $obtainToken = new ObtainToken($request->getToken());
                $obtainToken->setModel($model);

                $this->gateway->execute($obtainToken);
            }

            if (isset($model['requesttypedescriptions']) &&
                in_array('ACCOUNTCHECK', $model['requesttypedescriptions'])
            ) {
                $accountCheck = new AccountCheck($request->getToken());
                $accountCheck->setModel($model);

                $this->gateway->execute($accountCheck);
                return;
            }
        }

        if ($model['credentialsonfile'] === '2') {
            //TODO: Should probably move this logic out to a separate action
            $response = $this->api->recurringRequest($model->toUnsafeArrayWithoutLocal());
            $unwrappedResponse = Api::unwrapResponse($response);
            $model->replace($unwrappedResponse);
            return;
        }

        //TODO: Should probably move this logic out to a separate action
        $response = $this->api->chargeRequest($model->toUnsafeArrayWithoutLocal());
        $unwrappedResponse = Api::unwrapResponse($response);
        $model->replace($unwrappedResponse);
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return
            $request instanceof Capture &&
            $request->getModel() instanceof \ArrayAccess
            ;
    }
}
