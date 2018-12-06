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

        if ($model['status']) {
            return;
        }

        if (false == $model['cachetoken']) {
            $obtainToken = new ObtainToken($request->getToken());
            $obtainToken->setModel($model);

            $this->gateway->execute($obtainToken);
        }

        $response = $this->api->simpleChargeRequest($model->toUnsafeArrayWithoutLocal());
        if ($response) {
            if (is_array($response) && sizeof($response) === 1) {
                $model->replace($response[0]);
                return;
            }
        }
        throw new LogicException('Invalid response');
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
