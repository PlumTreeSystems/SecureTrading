<?php
/**
 * Created by PhpStorm.
 * User: marius
 * Date: 18.12.7
 * Time: 15.57
 */

namespace Tests\Action;


use Payum\Core\Request\GetHumanStatus;
use Payum\Core\Tests\GenericActionTest;
use PlumTreeSystems\SecureTrading\Action\StatusAction;

class StatusActionTest extends GenericActionTest
{

    protected $requestClass = GetHumanStatus::class;

    protected $actionClass = StatusAction::class;
    /**
     * @test
     */
    public function shouldMarkNewIfNoErrorCode()
    {
        $model = [];

        $action = new StatusAction();

        $action->execute($status = new GetHumanStatus($model));

        $this->assertTrue($status->isNew());
    }

    /** @test */
    public function shouldFailIfErrorCodeNon0()
    {
        $model = [
            'errorcode' => '1'
        ];

        $action = new StatusAction();

        $action->execute($status = new GetHumanStatus($model));

        $this->assertTrue($status->isFailed());
    }

    /** @test */
    public function shouldCompleteIfError0AndTypeDescriptionAuth()
    {
        $model = [
            'errorcode' => '0',
            'requesttypedescription' => 'AUTH'
        ];

        $action = new StatusAction();

        $action->execute($status = new GetHumanStatus($model));

        $this->assertTrue($status->isCaptured());
    }
}