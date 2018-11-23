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

        switch ($model['errorcode'])
        {
            case "0":
                $request->markAuthorized();
                return;
            case "30000":
                $request->markFailed();
                return;
            case "70000":
                $request->markFailed();
                return;
            case "60010":
            case "60034":
            case "99999":
                $request->markFailed();
                return;
        }

        if (!isset($model['errorcode']) && !isset($model['status'])) {
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
