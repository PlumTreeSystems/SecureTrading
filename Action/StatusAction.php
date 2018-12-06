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
                        }
                    }
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
