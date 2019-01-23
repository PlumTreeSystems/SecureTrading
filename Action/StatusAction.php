<?php
namespace PlumTreeSystems\SecureTrading\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Request\GetStatusInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;

class StatusAction implements ActionInterface
{
    /**
     * {@inheritDoc}
     *
     * @param GetStatusInterface $request
     * TODO go over this area again
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $model = ArrayObject::ensureArrayObject($request->getModel());
        if ($this->checkAcquirerAdviceCode($model, $request)) {
            $request->markFailed();
            return;
        }

        if (isset($model['errorcode'])) {
            switch ($model['errorcode'])
            {
                case "0":
                    if (isset($model['requesttypedescription'])) {
                        switch($model['requesttypedescription'])
                        {
                            case "AUTH":
                                $request->markCaptured();
                                return;
                            case "ACCOUNTCHECK":
                                $request->markAuthorized();
                                return;
                            case "STORE":
                                $request->markNew();
                                $model['errorcode'] = null;
                                return;
                        }
                    }
                    throw new \LogicException('Unhandled request type');
                default:
                    $request->markFailed();
                    return;
            }
        }


        if (!isset($model['errorcode']) && !isset($model['status'])) {
            $request->markNew();
            return;
        }

        if ($model['status'] === 'pending') {
            $request->markNew();
            return;
        }


        throw new \LogicException('Invalid Status');
    }

    private function checkAcquirerAdviceCode($model, GetStatusInterface $request)
    {
        if (isset($model['ackquireradvicecode'])) {
            switch ($model['ackquireradvicecode'])
            {
                case "0":
                    return false;
                case "1":
                    $model['ackquireradviceerror'] = "Need to update customers payment information";
                    return false;
                case "2":
                    $model['ackquireradviceerror'] = "Try again later";
                case "4":
                case "8":
                    $model['ackquireradviceerror'] = "Do not process further recurring transactions";
                    $model['errorcode'] = "1";
                    return true;
            }
        }
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return
            $request instanceof GetStatusInterface &&
            $request->getModel() instanceof \ArrayAccess
            ;
    }
}
