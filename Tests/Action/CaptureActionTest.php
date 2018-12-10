<?php
/**
 * Created by PhpStorm.
 * User: marius
 * Date: 18.12.7
 * Time: 14.06
 */

namespace Tests\Action;


use Payum\Core\ApiAwareInterface;
use Payum\Core\Exception\LogicException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\Request\Capture;
use Payum\Core\Tests\GenericActionTest;
use PlumTreeSystems\SecureTrading\Action\CaptureAction;
use PlumTreeSystems\SecureTrading\Api;
use PlumTreeSystems\SecureTrading\Request\Api\ObtainToken;

class CaptureActionTest extends GenericActionTest
{
    protected $requestClass = Capture::class;

    protected $actionClass = CaptureAction::class;

    /** @test */
    public function shouldImplementsApiAwareInterface()
    {
        $rc = new \ReflectionClass(CaptureAction::class);

        $this->assertTrue($rc->isSubclassOf(ApiAwareInterface::class));
    }

    /** @test */
    public function shouldImplementsGatewayAwareInterface()
    {
        $rc = new \ReflectionClass(CaptureAction::class);

        $this->assertTrue($rc->isSubclassOf(GatewayAwareInterface::class));
    }

    /**
     * @test
     */
    public function shouldDoNothingIfPaymentHasErrorCode()
    {
        $model = [
            'errorcode' => '0',
        ];

        $gatewayMock = $this->createGatewayMock();
        $gatewayMock
            ->expects($this->never())
            ->method('execute')
        ;

        $action = new CaptureAction();
        $action->setGateway($gatewayMock);

        $action->execute(new Capture($model));
    }

    /** @test */
    public function shouldInitiateObtainTokenActionWithoutCacheToken()
    {
        $model = [];
        $gatewayMock = $this->createGatewayMock();
        $gatewayMock
            ->expects($this->once())
            ->method('execute')
            ->with($this->isInstanceOf(ObtainToken::class))
        ;

        $apiMock = $this->createMock(Api::class);
        $apiMock
            ->expects($this->once())
            ->method('simpleChargeRequest')
            ->willReturn(['responses' => [['test_value' => 'test_value']]])
        ;

        $action = new CaptureAction();
        $action->setGateway($gatewayMock);
        $action->setApi($apiMock);

        $action->execute(new Capture($model));
    }

    /** @test */
    public function shouldPerformSimpleApiRequestIfHasCacheToken()
    {
        $model = [
            'cachetoken' => 'test_value',
        ];

        $gatewayMock = $this->createGatewayMock();
        $gatewayMock
            ->expects($this->never())
            ->method('execute')
        ;

        $apiMock = $this->createMock(Api::class);
        $apiMock
            ->expects($this->once())
            ->method('simpleChargeRequest')
            ->with(['cachetoken' => 'test_value'])
            ->willReturn(['responses' => [['test_value' => 'test_value']]])
        ;

        $action = new CaptureAction();
        $action->setGateway($gatewayMock);
        $action->setApi($apiMock);

        $action->execute(new Capture($model));
    }

    /** @test */
    public function shouldThrowIfInvalidResponseFormat()
    {
        $this->expectException(LogicException::class);

        $model = [
            'cachetoken' => 'test_value',
        ];

        $gatewayMock = $this->createGatewayMock();
        $gatewayMock
            ->expects($this->never())
            ->method('execute')
        ;

        $apiMock = $this->createMock(Api::class);
        $apiMock
            ->expects($this->once())
            ->method('simpleChargeRequest')
            ->willReturn(['responses' => 'test_value'])
        ;

        $action = new CaptureAction();
        $action->setGateway($gatewayMock);
        $action->setApi($apiMock);

        $action->execute(new Capture($model));
    }
}